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

use Magento\Cms\Model\Config\Source\Page as SourcePage;
use Magento\Framework\Data\Form\Element\CollectionFactory as ElementCollectionFactory;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Escaper;

class CmsPage extends Fieldset
{
    /**
     * Constructor
     *
     * @param ElementFactory           $factoryElement
     * @param ElementCollectionFactory $factoryCollection
     * @param Escaper                  $escaper
     * @param SourcePage               $configSourcePage
     * @param array                    $data
     */
    public function __construct(
        ElementFactory $factoryElement,
        ElementCollectionFactory $factoryCollection,
        Escaper $escaper,
        SourcePage $configSourcePage,
        $data = []
    ) {
        $this->configSourcePage = $configSourcePage;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $this->addField(
            'ignored_pages',
            'multiselect',
            [
                'name'     => 'properties[ignored_pages]',
                'label'    => __('Ignored Pages'),
                'required' => false,
                'values'   => $this->configSourcePage->toOptionArray(),
                'value'    => $this->getIndex()->getProperty('ignored_pages')
            ]
        );

        return parent::getHtml();
    }
}
