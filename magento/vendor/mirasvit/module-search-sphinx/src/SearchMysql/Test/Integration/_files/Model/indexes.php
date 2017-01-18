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


$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Framework\App\ResourceConnection $installer */
$installer = $objectManager->create('Magento\Framework\App\ResourceConnection');
$installer->getConnection()
    ->query('DELETE FROM ' . $installer->getTableName('mst_search_index'));
$installer->getConnection()
    ->query('ALTER TABLE ' . $installer->getTableName('mst_search_index') . ' AUTO_INCREMENT = 1;');

/** @var $index \Mirasvit\Search\Model\Index */
$index = $objectManager->create('Mirasvit\Search\Model\Index');
$index->load('catalogsearch_fulltext')
    ->setCode('catalogsearch_fulltext')
    ->setTitle('Product Index')
    ->setIsActive(true)
    ->setAttributes([
        'name'        => 10,
        'sku'         => 5,
        'description' => 9
    ])
    ->save();

$index->getIndexInstance()->reindexAll();

/** @var $index \Mirasvit\Search\Model\Index */
$index = $objectManager->create('Mirasvit\Search\Model\Index');
$index->load('magento_catalog_category')
    ->setCode('magento_catalog_category')
    ->setTitle('Category Index')
    ->setIsActive(true)
    ->setAttributes([
        'name'             => 10,
        'description'      => 5,
        'meta_title'       => 9,
        'meta_keywords'    => 7,
        'meta_description' => 7,
    ])
    ->save();

$index->getIndexInstance()->reindexAll();

$index = $objectManager->create('Mirasvit\Search\Model\Index');
$index->load('magento_cms_page')
    ->setCode('magento_cms_page')
    ->setTitle('Cms Page Index')
    ->setIsActive(true)
    ->setAttributes([
        'title'            => 10,
        'content'          => 5,
        'content_heading'  => 9,
        'meta_keywords'    => 7,
        'meta_description' => 7,
    ])
    ->save();

$index->getIndexInstance()->reindexAll();