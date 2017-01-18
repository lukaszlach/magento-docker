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
 * @package   mirasvit/module-search-sphinx
 * @version   1.0.34
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Block\Index;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Base extends Template
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Base constructor.
     *
     * @param Context                $context
     * @param ObjectManagerInterface $objectManager
     * @param array                  $data
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * Collection
     *
     * @return array
     */
    public function getCollection()
    {
        return $this->getIndex()->getIndexInstance()->getSearchCollection();
    }

    /**
     * Truncate text
     *
     * @param string $string
     * @return string
     */
    public function truncate($string)
    {
        if (strlen($string) > 512) {
            $string = strip_tags($string);
            $string = substr($string, 0, 512) . '...';
        }

        return $string;
    }

    /**
     * @return ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * Return pager html for current collection.
     *
     * @return html
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('pager');

        if (!$pager) {
            return '';
        }

        if (!$pager->getCollection()) {
            $pager->setCollection($this->getCollection());
        }

        return $pager->toHtml();
    }
}
