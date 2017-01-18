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


namespace Mirasvit\Search\Block\Adminhtml\Synonym;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Helper\Data as BackendHelper;
use Mirasvit\Search\Model\ResourceModel\Synonym\CollectionFactory;

class Grid extends ExtendedGrid
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Grid constructor.
     *
     * @param Context           $context
     * @param BackendHelper     $backendHelper
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $backendHelper);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('searchindexGrid');
        $this->setDefaultSort('synonym_id');
        $this->setDefaultDir('asc');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->collectionFactory->create());

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('term', [
            'header' => __('Term'),
            'type'   => 'text',
            'index'  => 'term'
        ])->addColumn('synonyms', [
            'header'   => __('Synonyms'),
            'type'     => 'text',
            'index'    => 'synonyms',
            'renderer' => 'Mirasvit\Search\Block\Adminhtml\Synonym\Grid\Column\Renderer\Synonyms',
        ])->addColumn('store_id', [
            'header' => __('Store'),
            'type'   => 'store',
            'index'  => 'store_id'
        ]);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('synonym_id');
        $this->getMassactionBlock()->setFormFieldName('synonym');

        $this->getMassactionBlock()->addItem('delete', [
            'label'   => __('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('search/synonym/edit', [
            'id' => $row->getId()
        ]);
    }
}
