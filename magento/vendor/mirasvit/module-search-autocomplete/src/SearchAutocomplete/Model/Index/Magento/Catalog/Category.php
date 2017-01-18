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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.0.36
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchAutocomplete\Model\Index\Magento\Catalog;

use Mirasvit\SearchAutocomplete\Model\Index\AbstractIndex;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryFactory as CatalogCategoryFactory;

class Category extends AbstractIndex
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CatalogCategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param StoreManagerInterface  $storeManager
     * @param CatalogCategoryFactory $categoryFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CatalogCategoryFactory $categoryFactory
    ) {
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->collection->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items = [];

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($this->collection as $category) {

            $parents = $this->getFullPath($category->getId());
            $names = [];
            foreach ($parents as $parent) {
                $names[] = $parent->getName();
            }
            $names[] = $category->getName();

            $items[] = [
                'name' => implode(' > ', $names),
                'url'  => $category->getUrl(),
            ];
        }

        return $items;
    }

    /**
     * List of parent categories
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Model\Category[]
     */
    public function getFullPath($categoryId)
    {
        $store = $this->storeManager->getStore();
        $rootId = $store->getRootCategoryId();

        $result = [];
        $id = $categoryId;

        do {
            $parent = $this->categoryFactory->create()->load($id)->getParentCategory();
            $id = $parent->getId();

            if (!$parent->getId()) {
                break;
            }

            if (!$parent->getIsActive() && $parent->getId() != $rootId) {
                break;//return false;
            }

            if ($parent->getId() != $rootId) {
                $result[] = $parent;
            }
        } while ($parent->getId() != $rootId);

        $result = array_reverse($result);

        return $result;
    }
}
