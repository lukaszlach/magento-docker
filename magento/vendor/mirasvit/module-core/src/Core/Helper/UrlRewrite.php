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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Filter\FilterManager;
use Mirasvit\Core\Api\UrlRewriteHelperInterface;
use Mirasvit\Core\Model\ResourceModel\UrlRewrite\CollectionFactory as UrlRewriteCollectionFactory;
use Mirasvit\Core\Model\UrlRewriteFactory;

class UrlRewrite extends AbstractHelper implements UrlRewriteHelperInterface
{
    /**
     * @var UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $urlRewriteCollectionFactory;

    /**
     * @var FilterManager
     */
    protected $filter;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $configB = [];


    /**
     * {@inheritdoc}
     *
     * @param UrlRewriteFactory           $urlRewriteFactory
     * @param UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param FilterManager               $filter
     * @param Context                     $context
     */
    public function __construct(
        UrlRewriteFactory $urlRewriteFactory,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        FilterManager $filter,
        Context $context
    ) {
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->filter = $filter;
        $this->urlManager = $context->getUrlBuilder();
        $this->scopeConfig = $context->getScopeConfig();

        parent::__construct($context);
    }

    /**
     * Is enabled rewrites for module?
     *
     * @param string $module module alias (kbase)
     * @return bool
     */
    public function isEnabled($module)
    {
        if (!isset($this->config[$module])) {
            return false;
        }
        if (isset($this->config[$module]['_ENABLED'])) {
            return $this->config[$module]['_ENABLED'];
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setRewriteMode($module, $isEnabled)
    {
        $this->config[$module]['_ENABLED'] = $isEnabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBasePath($module, $path)
    {
        $this->config[$module]['_BASE_PATH'] = $path;
        $this->configB[$path] = $module;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerPath($module, $type, $template, $action, $params = [])
    {
        $this->config[$module][$type] = $template;
        $this->configB[$module . '_' . $type]['ACTION'] = $action;
        $this->configB[$module . '_' . $type]['PARAMS'] = $params;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($module, $type, $entity = null)
    {
        if ($this->isEnabled($module)) {
            $basePath = $this->config[$module]['_BASE_PATH'];

            if ($entity) {
                $collection = $this->urlRewriteCollectionFactory->create()
                    ->addFieldToFilter('module', $module)
                    ->addFieldToFilter('type', $type)
                    ->addFieldToFilter('entity_id', $entity->getId());
                if ($collection->count()) {
                    $rewrite = $collection->getFirstItem();
                    return $this->getUrlByKey($basePath, $rewrite->getUrlKey());
                } else {
                    return $this->getDefaultUrl($module, $type, $entity);
                }
            } else {
                return $this->getUrlByKey($basePath, $this->config[$module][$type]);
            }
        } else {
            return $this->getDefaultUrl($module, $type, $entity);
        }
    }

    /**
     * Return unique path (recursive check)
     *
     * @param string $module   module alias (kbase)
     * @param string $type     path type (category, article etc)
     * @param string $path     path url key
     * @param string $entityId entity id
     * @param int    $i        additional
     * @return string
     */
    protected function getUniquePath($module, $type, $path, $entityId, $i = 0)
    {
        if ($i) {
            $pathToCheck = $path . '-' . $i;
        } else {
            $pathToCheck = $path;
        }

        // check path for duplicates
        $collection = $this->urlRewriteCollectionFactory->create()
            ->addFieldToFilter('module', $module)
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('url_key', $pathToCheck)
            ->addFieldToFilter('entity_id', ['neq' => $entityId])
            ->setOrder('url_key', 'asc');

        if ($collection->count()) {
            return $this->getUniquePath($module, $type, $path, $entityId, ++$i);
        }

        return $pathToCheck;
    }

    /**
     * {@inheritdoc}
     */
    public function updateUrlRewrite($module, $type, $entity, $values)
    {
        if (!isset($this->config[$module])) {
            return false;
        }

        $objectId = $entity->getId();
        $pathTemplate = $this->config[$module][$type];
        $path = $pathTemplate;

        foreach ($values as $key => $value) {
            $path = str_replace("[$key]", $value, $path);
        }

        $path = trim($path, '/');
        $path = $this->getUniquePath($module, $type, $path, $objectId);

        $collection = $this->urlRewriteCollectionFactory->create()
            ->addFieldToFilter('module', $module)
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('entity_id', $objectId);
        if ($collection->count()) {
            /** @var \Mirasvit\Core\Model\UrlRewrite $rewrite */
            $rewrite = $collection->getFirstItem();
            $rewrite->setUrlKey($path)
                ->save();
        } else {
            $rewrite = $this->urlRewriteFactory->create();
            $rewrite
                ->setModule($module)
                ->setType($type)
                ->setEntityId($objectId)
                ->setUrlKey($path)
                ->save();
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUrlRewrite($module, $type, $entity)
    {
        $collection = $this->urlRewriteCollectionFactory->create()
            ->addFieldToFilter('module', $module)
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('entity_id', $entity->getId());
        if ($collection->count()) {
            /** @var \Mirasvit\Core\Model\UrlRewrite $rewrite */
            $rewrite = $collection->getFirstItem();
            $rewrite->delete();
        }

        return true;
    }

    /**
     * Absolute default url
     *
     * @param string $module
     * @param string $type
     * @param string $object
     * @return string
     */
    protected function getDefaultUrl($module, $type, $object)
    {
        if (!isset($this->configB[$module . '_' . $type])) {
            return '';
        }

        $action = $this->configB[$module . '_' . $type]['ACTION'];
        $params = $this->configB[$module . '_' . $type]['PARAMS'];

        $action = str_replace('_', '/', $action);
        if ($object) {
            $params['id'] = $object->getId();
        }

        return $this->urlManager->getUrl($action, $params);
    }

    /**
     * Absolute url by key
     *
     * @param string     $basePath
     * @param string     $urlKey
     * @param bool|false $params
     * @return string
     */
    protected function getUrlByKey($basePath, $urlKey, $params = false)
    {
        if ($urlKey) {
            $url = $basePath . '/' . $urlKey;
        } else {
            $url = $basePath;
        }
        $configUrlSuffix = $this->scopeConfig->getValue('catalog/seo/product_url_suffix');
        //user can enter .html or html suffix
        if ($configUrlSuffix != '' && $configUrlSuffix[0] != '.') {
            $configUrlSuffix = '.' . $configUrlSuffix;
        }
        if (substr($url, -strlen($configUrlSuffix)) == $configUrlSuffix) {
            $url = substr($url, 0, -strlen($configUrlSuffix));
        }
        $url .= $configUrlSuffix;
        if ($params) {
            $url .= '?' . http_build_query($params);
        }
        $url = $this->urlManager->getDirectUrl($url);

        return $url;
    }

    /**
     * Return url without suffix
     *
     * @param string $key
     * @return string
     */
    protected function getUrlKeyWithoutSuffix($key)
    {
        $configUrlSuffix = $this->scopeConfig->getValue('catalog/seo/product_url_suffix');
        //user can enter .html or html suffix
        if ($configUrlSuffix != '' && $configUrlSuffix[0] != '.') {
            $configUrlSuffix = '.' . $configUrlSuffix;
        }
        $key = str_replace($configUrlSuffix, '', $key);

        return $key;
    }

    /**
     * Math path
     *
     * @param string $pathInfo
     * @return bool|DataObject
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *  @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function match($pathInfo)
    {
        $identifier = trim($pathInfo, '/');
        $parts = explode('/', $identifier);
        if (count($parts) == 1) {
            $parts[0] = $this->getUrlKeyWithoutSuffix($parts[0]);
        }

        if (isset($parts[0]) && !isset($this->configB[$parts[0]])) {
            return false;
        }
        $module = $this->configB[$parts[0]];

        if (!$this->isEnabled($module)) {
            return false;
        }
        if (count($parts) > 1) {
            unset($parts[0]);
            $urlKey = implode('/', $parts);
            $urlKey = urldecode($urlKey);
            $urlKey = $this->getUrlKeyWithoutSuffix($urlKey);
        } else {
            $urlKey = '';
        }

        # check on static urls (urls for static pages, ex. lists)
        $type = $rewrite = false;
        foreach ($this->config[$module] as $t => $key) {
            if ($key === $urlKey) {
                if ($t == '_BASE_PATH') {
                    continue;
                }
                $type = $t;
                break;
            }
        }

        # check on dynamic urls (ex. urls of products, categories etc)
        if (!$type) {
            $collection = $this->urlRewriteCollectionFactory->create()
                ->addFieldToFilter('url_key', $urlKey)
                ->addFieldToFilter('module', $module);
            if ($collection->count()) {
                /** @var \Mirasvit\Core\Model\UrlRewrite $rewrite */
                $rewrite = $collection->getFirstItem();
                $type = $rewrite->getType();
            } else {
                return false;
            }
        }
        if ($type) {
            $action = $this->configB[$module . '_' . $type]['ACTION'];
            $params = $this->configB[$module . '_' . $type]['PARAMS'];
            $result = new DataObject();
            $actionParts = explode('_', $action);

            $result->addData([
                'route_name'      => $actionParts[0],
                'module_name'     => $actionParts[0],
                'controller_name' => $actionParts[1],
                'action_name'     => $actionParts[2],
                'action_params'   => $params,
            ]);

            if ($rewrite) {
                $result->setData('entity_id', $rewrite->getEntityId());
            }

            return $result;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($string)
    {
        //@codingStandardsIgnoreStart
        $table = [
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c',
            'Ć' => 'C', 'ć' => 'c', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae', 'Å' => 'A',
            'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'Oe',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'ae', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'Ŕ' => 'R',
            'ŕ' => 'r', 'ü' => 'ue', '/' => '', '&' => '', '(' => '', ')' => '',
        ];
        //@codingStandardsIgnoreStop

        $string = strtr($string, $table);
        $string = $this->filter->translitUrl($string);

        return $string;
    }
}
