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


namespace Mirasvit\Search\Block\Adminhtml\Index\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Mirasvit\Search\Model\Config;

class Status extends AbstractRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(DataObject $row)
    {
        /** @var \Mirasvit\Search\Model\Index $row */
        $status = $row->getStatus();
        $isActive = $row->getIsActive();

        $label = __('Disabled');
        $class = 'grid-severity-major';

        if ($isActive) {
            if ($status == Config::INDEX_STATUS_READY) {
                $class = 'grid-severity-notice';
                $label = __('Ready');
            } else {
                $class = 'grid-severity-critical';
                $label = __('Reindex Required');
            }
        }

        return "<span class='$class'><span>$label</span></span>";
    }
}
