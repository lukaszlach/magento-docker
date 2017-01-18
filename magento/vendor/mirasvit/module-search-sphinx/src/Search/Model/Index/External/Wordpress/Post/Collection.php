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


namespace Mirasvit\Search\Model\Index\External\Wordpress\Post;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /**
     * Constructor
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface        $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param EventManagerInterface  $eventManager
     * @param null                   $index
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        EventManagerInterface $eventManager,
        $index = null
    ) {
        /** @var \Mirasvit\Search\Model\Index\External\Wordpress\Post\Index $index */
        $this->index = $index;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);

        $this->setConnection($this->index->getConnection());

        $this->setModel('\Mirasvit\Search\Model\Index\External\Wordpress\Post\Item');

        $this->_initSelect();
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->where('main_table.post_status=?', 'publish');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMainTable()
    {
        return $this->index->getModel()->getProperty('db_table_prefix') . 'posts';
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceModelName()
    {
        return 'Mirasvit\Search\Model\ResourceModel\Index';
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $item->setIndex($this->index);
        }

        return parent::_afterLoad();
    }
}
