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
    ->query('DELETE FROM ' . $installer->getTableName('mst_search_synonym'));
$installer->getConnection()
    ->query('ALTER TABLE ' . $installer->getTableName('mst_search_synonym') . ' AUTO_INCREMENT = 1;');

/** @var $synonym \Mirasvit\Search\Model\Synonym */
$synonym = $objectManager->create('Mirasvit\Search\Model\Synonym');
$synonym->setTerm('cat')
    ->setSynonyms('dog, animal')
    ->save();

/** @var $synonym \Mirasvit\Search\Model\Synonym */
$synonym = $objectManager->create('Mirasvit\Search\Model\Synonym');
$synonym->setTerm('ring')
    ->setSynonyms('earning, jewelry')
    ->save();
