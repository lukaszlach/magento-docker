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

class Menu extends Template
{
    /**
     * @var array
     */
    protected $pool;

    /**
     * @var AbstractMenu
     */
    protected $activeMenu;

    /**
     * @param Context $context
     * @param array   $menu
     */
    public function __construct(
        Context $context,
        $menu = []
    ) {
        $this->pool = $menu;

        parent::__construct($context);
    }

    /**
     * @return AbstractMenu
     */
    public function getActiveMenu()
    {
        if (!$this->activeMenu) {
            /** @var AbstractMenu $menu */
            foreach ($this->pool as $menu) {
                if ($menu->isVisible()) {
                    $menu->build();
                    $this->activeMenu = $menu;
                    break;
                }
            }
        }

        return $this->activeMenu;
    }

    /**
     * @return string
     */
    public function getActiveTitle()
    {
        if ($this->getActiveMenu()) {
            return $this->getActiveMenu()->getActiveTitle();
        }

        return '';
    }

    /**
     * @return array
     */
    public function getItems()
    {
        if ($this->getActiveMenu()) {
            return $this->getActiveMenu()->getItems();
        }

        return [];
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getActiveMenu()) {
            return parent::_toHtml();
        }

        return false;
    }
}