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



namespace Mirasvit\Core\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;

abstract class AbstractMenu extends Template
{
    const SEPARATOR = 'separator';

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var DataObject
     */
    protected $activeItem;

    /**
     * @var array
     */
    protected $visibleAt = [];

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);

        $this->context = $context;
        $this->urlBuilder = $this->_urlBuilder;
    }

    /**
     * Build menu
     *
     * @return $this
     */
    protected abstract function buildMenu();

    /**
     * Set menu visibility
     * @param array $modules alias of modules (email, search, helpdesk)
     * @return $this
     */
    protected function visibleAt($modules)
    {
        if (!is_array($modules)) {
            $modules = [$modules];
        }

        $this->visibleAt = $modules;

        return $this;
    }

    /**
     * @return $this
     */
    public function build()
    {
        if (!$this->isVisible()) {
            return parent::_prepareLayout();
        }

        $this->buildMenu();
        $currentUrl = $this->urlBuilder->getCurrentUrl();

        /** @var DataObject $item */
        foreach ($this->getFlatTree() as $item) {
            if (!is_object($item)) {
                continue;
            }

            if ($item->getData('url') == $currentUrl) {
                $this->activeItem = $item;
                break;
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array       $data
     * @param string|null $parent
     * @return $this
     */
    public function addItem($data, $parent = null)
    {
        $auth = $this->getAuthorization();

        if (is_array($data) && !$auth->isAllowed($data['resource'])) {
            return $this;
        }

        if ($parent !== null) {
            /** @var DataObject $item */
            foreach ($this->getFlatTree() as $item) {
                if ($item->getData('id') == $parent) {
                    $items = $item->getData('items');
                    $items[] = new DataObject($data);

                    $item->setData('items', $items);
                    break;

                }
            }
        } else {
            $this->items[] = new DataObject($data);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addSeparator()
    {
        $this->items[] = self::SEPARATOR;

        return $this;
    }

    /**
     * @return string
     */
    public function getActiveTitle()
    {
        if ($this->activeItem) {
            return $this->activeItem->getData('title');
        } else {
            return $this->context->getPageConfig()->getTitle()->getShort();
        }
    }

    /**
     * @param null|array $items
     * @return void
     */
    protected function getFlatTree($items = null)
    {
        if (!$items) {
            $items = $this->items;
        }

        /** @var DataObject $item */
        foreach ($items as $item) {
            yield $item;

            if (is_object($item) && $item->hasData('items')) {
                foreach ($this->getFlatTree($item->getData('items')) as $subitem) {
                    yield $subitem;
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        if (in_array($this->getRequest()->getModuleName(), $this->visibleAt)) {
            return true;
        }

        return false;
    }
}