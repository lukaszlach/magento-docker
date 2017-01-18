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


namespace Mirasvit\Search\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mirasvit\Search\Model\IndexFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var IndexFactory
     */
    protected $indexFactory;

    /**
     * @param IndexFactory $indexFactory
     */
    public function __construct(
        IndexFactory $indexFactory
    ) {
        $this->indexFactory = $indexFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->indexFactory->create()->load('catalogsearch_fulltext')
            ->setCode('catalogsearch_fulltext')
            ->setTitle('Products')
            ->setIsActive(true)
            ->setPosition(1)
            ->save();

        $this->indexFactory->create()->load('magento_catalog_category')
            ->setCode('magento_catalog_category')
            ->setTitle('Categories')
            ->setIsActive(false)
            ->setPosition(2)
            ->setAttributes([
                'name'             => 10,
                'description'      => 5,
                'meta_title'       => 9,
                'meta_keywords'    => 1,
                'meta_description' => 1,
            ])
            ->save();

        $this->indexFactory->create()->load('magento_cms_page')
            ->setCode('magento_cms_page')
            ->setTitle('Information')
            ->setIsActive(false)
            ->setPosition(3)
            ->setAttributes([
                'title'            => 10,
                'content'          => 5,
                'content_heading'  => 9,
                'meta_keywords'    => 1,
                'meta_description' => 1,
            ])
            ->save();
    }
}
