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
 * @package   mirasvit/module-misspell
 * @version   1.0.7
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\ResourceConnection $installer */
$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()
    ->query('DELETE FROM ' . $installer->getTableName('catalog_product_entity'));
$installer->getConnection()
    ->query('ALTER TABLE ' . $installer->getTableName('catalog_product_entity') . ' AUTO_INCREMENT = 1;');

/** @var $product \Magento\Catalog\Model\Product */
$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Nokia A55')
    ->setUrlKey('nokia-a55'.microtime(true))
    ->setSku('ABC-1234')
    ->setPrice(10.22)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Samsung Gallaxy S4')
    ->setUrlKey('samsung-gallaxy-s4'.microtime(true))
    ->setSku('fulltext-2')
    ->setPrice(423)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

$indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Indexer\Model\Indexer'
);
$indexer->load('catalogsearch_fulltext');
$indexer->reindexAll();
