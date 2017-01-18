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


namespace Mirasvit\Search\Block\Adminhtml\Index;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Magento\Backend\Helper\Data as BackendHelper;
use Mirasvit\Search\Model\Config\Source\Index as SourceIndex;
use Mirasvit\Search\Model\ResourceModel\Index\CollectionFactory as IndexCollectionFactory;

class Grid extends GridExtended
{
    /**
     * Constructor
     *
     * @param Context                $context
     * @param BackendHelper          $backendHelper
     * @param IndexCollectionFactory $collectionFactory
     * @param SourceIndex            $sourceIndex
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        IndexCollectionFactory $collectionFactory,
        SourceIndex $sourceIndex
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->sourceIndex = $sourceIndex;

        parent::__construct($context, $backendHelper);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('searchindexGrid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('asc');
        $this->setFilterVisibility(false);
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
        $this->addColumn('id', [
                'header' => __('ID'),
                'type'   => 'number',
                'index'  => 'index_id',
            ]
        )->addColumn('title', [
                'header' => __('Title'),
                'index'  => 'title',
            ]
        )->addColumn('code', [
                'header'  => __('Type'),
                'type'    => 'options',
                'index'   => 'code',
                'options' => $this->sourceIndex->toOptionArray(),
            ]
        )->addColumn('position', [
                'header' => __('Position'),
                'type'   => 'number',
                'index'  => 'position'
            ]
        )->addColumn('status', [
                'header'   => __('Status'),
                'type'     => 'options',
                'renderer' => 'Mirasvit\Search\Block\Adminhtml\Index\Grid\Column\Renderer\Status',
                'index'    => 'status_id'
            ]
        )->addColumn('action', [
                'header'   => __('Action'),
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => [
                    [
                        'caption' => __('Reindex'),
                        'url'     => [
                            'base' => 'search/index/reindex',
                        ],
                        'field'   => 'id',
                    ],
                ],
                'sortable' => false,
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('search/index/edit', ['id' => $row->getId()]);
    }
}
