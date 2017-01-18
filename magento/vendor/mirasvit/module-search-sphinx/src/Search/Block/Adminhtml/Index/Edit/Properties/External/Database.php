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


namespace Mirasvit\Search\Block\Adminhtml\Index\Edit\Properties\External;

use Magento\Framework\Data\Form\Element\Fieldset;
use Mirasvit\Search\Model\Index;

/**
 * @method Index getIndex()
 */
class Database extends Fieldset
{
    /**
     * {@inheritdoc}
     */
    public function getLegend()
    {
        return __('Database Connection');
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $this->addField(
            'db_connection_name',
            'text',
            [
                'name'     => 'properties[db_connection_name]',
                'label'    => __('Database Connection Name'),
                'required' => true,
                'value'    => $this->getIndex()->getProperty('db_connection_name')
            ]
        );

        $this->addField(
            'db_table_prefix',
            'text',
            [
                'name'     => 'properties[db_table_prefix]',
                'label'    => __('Table Prefix'),
                'required' => false,
                'value'    => $this->getIndex()->getProperty('db_table_prefix')
            ]
        );

        return parent::getHtml();
    }
}
