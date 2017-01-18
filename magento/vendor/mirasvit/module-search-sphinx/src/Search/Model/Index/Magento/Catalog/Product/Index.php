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


namespace Mirasvit\Search\Model\Index\Magento\Catalog\Product;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Config as EavConfig;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Search\Model\Config;
use Mirasvit\Search\Model\Index\AbstractIndex;
use Mirasvit\Search\Model\Index\IndexerFactory;
use Mirasvit\Search\Model\Index\SearcherFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Index extends AbstractIndex
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $attributeToCode;

    /**
     * @var AttributeCollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var LayerResolver
     */
    protected $layerResolver;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @param AttributeCollectionFactory $attributeCollectionFactory
     * @param Attribute                  $eavAttribute
     * @param LayerResolver              $layerResolver
     * @param EavConfig                  $eavConfig
     * @param Config                     $config
     * @param ObjectManagerInterface     $objectManager
     * @param IndexerFactory             $indexer
     * @param SearcherFactory            $searcher
     */
    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory,
        Attribute $eavAttribute,
        LayerResolver $layerResolver,
        EavConfig $eavConfig,
        Config $config,
        ObjectManagerInterface $objectManager,
        IndexerFactory $indexer,
        SearcherFactory $searcher
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->eavAttribute = $eavAttribute;
        $this->layerResolver = $layerResolver;
        $this->eavConfig = $eavConfig;
        $this->config = $config;
        $this->objectManager = $objectManager;

        parent::__construct($indexer, $searcher);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Product')->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return __('Magento')->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'catalogsearch_fulltext';
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsets()
    {
        return ['\Mirasvit\Search\Block\Adminhtml\Index\Edit\Properties\Magento\Catalog\Product'];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($extended = false)
    {
        if (!$this->attributes) {
            $collection = $this->attributeCollectionFactory->create()
                ->addVisibleFilter()
                ->setOrder('attribute_id', 'asc');

            /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            foreach ($collection as $attribute) {
                $allLockedFields = $this->eavConfig->get(
                    $attribute->getEntityType()->getEntityTypeCode() . '/attributes/' . $attribute->getAttributeCode()
                );
                if (!is_array($allLockedFields)) {
                    $allLockedFields = [];
                }

                if ($attribute->getDefaultFrontendLabel() && !isset($allLockedFields['is_searchable'])) {
                    $this->attributes[$attribute->getAttributeCode()] = $attribute->getDefaultFrontendLabel();
                }
            }
        }

        $result = $this->attributes;

        if ($extended) {
            $result['visibility'] = '';
            $result['options'] = '';
            $result['category_ids'] = '';
            $result['status'] = '';
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeId($attributeCode)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode($attributeId)
    {
        if (!isset($this->attributeToCode[$attributeId])) {
            $attribute = $this->attributeCollectionFactory->create()
                ->getItemByColumnValue('attribute_id', $attributeId);

            $this->attributeToCode[$attributeId] = $attribute['attribute_code'];

        }

        return $this->attributeToCode[$attributeId];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeWeights()
    {
        $result = [];
        $collection = $this->attributeCollectionFactory->create()
            ->addVisibleFilter()
            ->setOrder('search_weight', 'desc');

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        foreach ($collection as $attribute) {
            if ($attribute->getIsSearchable()) {
                $result[$attribute->getAttributeCode()] = $attribute->getSearchWeight();
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        return 'entity_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildSearchCollection()
    {
        /** @var \Magento\Catalog\Model\Layer\Search $layer */
        $layer = $this->layerResolver->get();

        if ($this->config->isMultiStoreModeEnabled()) {
            $originalCategory = $layer->getData('current_category');
            // set random category for multi-store mode
            // this mode can be not compatible with some layered navigation
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $category = $objectManager->create('Magento\Catalog\Model\Category');
            $category->setId(rand(100000, 900000));
            $layer->setData('current_category', $category);
        }

        $collection = $layer->getProductCollection();

        if ($this->config->isMultiStoreModeEnabled()) {
            $layer->setData('current_category', $originalCategory);
        }

        return $collection;
    }

    /**
     * Save search weights for catalog attributes
     * {@inheritdoc}
     */
    public function afterModelSave()
    {
        $attributes = $this->getModel()->getData('attributes');

        if (!is_array($attributes)) {
            return parent::afterModelSave();
        }

        $entityTypeId = $this->objectManager->create('Magento\Eav\Model\Entity')->setType(Product::ENTITY)->getTypeId();

        $collection = $this->attributeCollectionFactory->create()
            ->addFieldToFilter('is_searchable', 1);
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        foreach ($collection as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $attributes) && $attribute->getIsSearchable()) {
                $attribute->setIsSearchable(0)
                    ->save();
            }
        }

        foreach ($attributes as $code => $weight) {
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute = $this->eavAttribute->loadByCode($entityTypeId, $code);
            $attribute->setSearchWeight($weight)
                ->setIsSearchable(1)
                ->save();
        }

        return parent::afterModelSave();
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableEntities($storeId, $entityIds = null, $lastEntityId = null, $limit = 100)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function reindexAll()
    {
        $fulltext = $this->objectManager->create(
            '\Magento\CatalogSearch\Model\Indexer\Fulltext',
            [
                'data' => [
                    'fieldsets'  => [],
                    'indexer_id' => 'catalogsearch_fulltext',
                ]
            ]
        );
        $fulltext->executeFull();

        $this->getModel()
            ->setStatus(Config::INDEX_STATUS_READY)
            ->save();

        return true;
    }
}
