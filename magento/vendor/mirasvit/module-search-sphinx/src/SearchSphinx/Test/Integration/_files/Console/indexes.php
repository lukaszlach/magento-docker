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
$installer->getConnection()->truncateTable('mst_search_index');

/** @var $index \Mirasvit\Search\Model\Index */
$index = $objectManager->create('Mirasvit\Search\Model\Index');
$index->load('catalogsearch_fulltext')
    ->setCode('catalogsearch_fulltext')
    ->setTitle('Product Index')
    ->setIsActive(true)
    ->setAttributes([
        'name'        => 100,
        'sku'         => 10,
        'description' => 9
    ])
    ->save();

/** @var $index \Mirasvit\Search\Model\Index */
$index = $objectManager->create('Mirasvit\Search\Model\Index');
$index->load('magento_catalog_category')
    ->setCode('magento_catalog_category')
    ->setTitle('Category Index')
    ->setIsActive(true)
    ->setAttributes([
        'name'             => 100,
        'description'      => 10,
        'meta_title'       => 9,
        'meta_keywords'    => 7,
        'meta_description' => 7,
    ])
    ->save();

$index = $objectManager->create('Mirasvit\Search\Model\Index');
$index->load('magento_cms_page')
    ->setCode('magento_cms_page')
    ->setTitle('Cms Page Index')
    ->setIsActive(true)
    ->setAttributes([
        'title'            => 100,
        'content'          => 10,
        'content_heading'  => 9,
        'meta_keywords'    => 7,
        'meta_description' => 7,
    ])
    ->save();
