<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-core
 * @version   1.2.11
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Helper;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Mirasvit\Core\Api\ParseVariablesHelperInterface;

class ParseVariables extends AbstractHelper implements ParseVariablesHelperInterface
{
    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * Constructor
     *
     * @param Context       $context
     * @param PricingHelper $pricingHelper
     * @param EavConfig     $eavConfig
     */
    public function __construct(
        Context $context,
        PricingHelper $pricingHelper,
        EavConfig $eavConfig
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->eavConfig = $eavConfig;

        parent::__construct($context);
    }

    /**
     * Parse string.
     * [product_name][, model: {product_model}!] [product_nonexists]  [buy it {product_nonexists} !]
     *
     * @param string   $str
     * @param array    $objects
     * @param array    $additional
     * @param bool|int $storeId
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *  @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function parse($str, $objects, $additional = [], $storeId = false)
    {
        if (trim($str) == '') {
            return $str;
        }

        $bAOpen = '[ZZZZZ';
        $bAClose = 'ZZZZZ]';
        $bBOpen = '{WWWWW';
        $bBClose = 'WWWWW}';

        $str = str_replace('[', $bAOpen, $str);
        $str = str_replace(']', $bAClose, $str);
        $str = str_replace('{', $bBOpen, $str);
        $str = str_replace('}', $bBClose, $str);

        $pattern = '/\[ZZZZZ[^ZZZZZ\]]*ZZZZZ\]/';

        preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);

        $vars = [];
        foreach ($matches as $match) {
            $vars[$match[0]] = $match[0];
        }

        foreach ($objects as $key => $object) {
            $data = $object->getData();
            if (isset($additional[$key])) {
                $data = array_merge($data, $additional[$key]);
            }

            foreach ($data as $dataKey => $value) {
                if (is_array($value) || is_object($value)) {
                    continue;
                }

                $kA = $bBOpen . $key . '_' . $dataKey . $bBClose;
                $kB = $bAOpen . $key . '_' . $dataKey . $bAClose;
                $skip = true;

                foreach ($vars as $k => $v) {
                    if (stripos($v, $kA) !== false || stripos($v, $kB) !== false) {
                        $skip = false;
                        break;
                    }
                }

                if ($skip) {
                    continue;
                }

                $value = $this->checkForConvert($object, $key, $dataKey, $value, $storeId);
                foreach ($vars as $k => $v) {
                    if ($value == '') {
                        if (stripos($v, $kA) !== false || stripos($v, $kB) !== false) {
                            $vars[$k] = '';
                            continue;
                        }
                    }

                    $v = str_replace($kA, $value, $v);
                    $v = str_replace($kB, $value, $v);
                    $vars[$k] = $v;
                }
            }
        }

        foreach ($vars as $k => $v) {
            //if no attibute like [product_nonexists]
            if ($v == $k) {
                $v = '';
            }

            //remove start and end symbols from the string (trim)
            if (substr($v, 0, strlen($bAOpen)) == $bAOpen) {
                $v = substr($v, strlen($bAOpen), strlen($v));
            }

            if (strpos($v, $bAClose) === strlen($v) - strlen($bAClose)) {
                $v = substr($v, 0, strlen($v) - strlen($bAClose));
            }

            //if no attribute like [buy it {product_nonexists} !]
            if (stripos($v, $bBOpen) !== false || stripos($v, $bAOpen) !== false) {
                $v = '';
            }

            $str = str_replace($k, $v, $str);
        }

        return $str;
    }

    /**
     * Return object value by key
     *
     * @param object $object
     * @param string $key
     * @param string $dataKey
     * @param string $value
     * @param int    $storeId
     * @return float|string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function checkForConvert($object, $key, $dataKey, $value, $storeId)
    {
        if ($key == 'product' || $key == 'category') {
            if ($key == 'product') {
                $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $dataKey);
            } else {
                $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Category::ENTITY, $dataKey);
            }

            if ($storeId) {
                $attribute->setStoreId($storeId);
            }

            if ($attribute->getId() > 0) {
                try {
                    $valueId = $object->getDataUsingMethod($dataKey);
                    $value = $attribute->getFrontend()->getValue($object);
                } catch (\Exception $e) {
                    //possible that some extension is removed, but we have it attribute with source in database
                    $value = '';
                }

                if ($value == 'No' && $valueId == '') {
                    $value = '';
                }

                switch ($dataKey) {
                    case 'price':
                        $value = $this->pricingHelper->currency($value, true, false);
                        break;
                    case 'special_price':
                        $value = $this->pricingHelper->currency($value, true, false);
                        break;
                }
            } else {
                switch ($dataKey) {
                    case 'final_price':
                        $value = $this->pricingHelper->currency($value, true, false);
                        break;
                }
            }
        }

        if (is_array($value)) {
            if (isset($value['label'])) {
                $value = $value['label'];
            } else {
                $value = '';
            }
        }

        return $value;
    }
}
