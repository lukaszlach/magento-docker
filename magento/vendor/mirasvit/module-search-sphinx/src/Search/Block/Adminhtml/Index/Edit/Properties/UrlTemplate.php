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


namespace Mirasvit\Search\Block\Adminhtml\Index\Edit\Properties;

use Magento\Framework\Data\Form\Element\Fieldset;

class UrlTemplate extends Fieldset
{
    /**
     * {@inheritdoc}
     */
    public function getLegend()
    {
        return __('Url Template');
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $this->addField(
            'url_template',
            'text',
            [
                'name'     => 'properties[url_template]',
                'label'    => __('Url Template'),
                'required' => true,
                'value'    => $this->getIndex()->getProperty('url_template')
            ]
        );

        return parent::getHtml();
    }
}
