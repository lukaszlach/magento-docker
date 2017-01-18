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


namespace Mirasvit\Search\Block\Adminhtml\Index\Edit\Properties\Magento\Catalog;

use Magento\Framework\Data\Form\Element\Fieldset;
use Mirasvit\Search\Model\Index;

/**
 * @method Index getIndex()
 */
class Product extends Fieldset
{
    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $checked = [];
        if ($this->getIndex()->hasProperty('include_category')) {
            $checked[] = 'include_category';
        }
        if ($this->getIndex()->hasProperty('include_bundled')) {
            $checked[] = 'include_bundled';
        }
        if ($this->getIndex()->hasProperty('include_id')) {
            $checked[] = 'include_id';
        }
        if ($this->getIndex()->hasProperty('include_custom_options')) {
            $checked[] = 'include_custom_options';
        }
        if ($this->getIndex()->hasProperty('out_of_stock_to_end')) {
            $checked[] = 'out_of_stock_to_end';
        }
        if ($this->getIndex()->hasProperty('only_active_categories')) {
            $checked[] = 'only_active_categories';
        }

        $this->addField('properties', 'checkboxes', [
            'name'    => 'properties[]',
            'label'   => __('Options'),
            'values'  => [
                'include_category'       => __('Search by parent categories names'),
                'include_bundled'        => __('Search by child products (for bundle, configurable products)'),
                'include_id'             => __('Search by product id'),
                'include_custom_options' => __('Search by custom options'),
                'out_of_stock_to_end'    => __('Push "out of stock" products to the end'),
                'only_active_categories' => __('Search only by active categories'),
            ],
            'checked' => $checked,
        ]);


        return parent::getHtml();
    }
}
