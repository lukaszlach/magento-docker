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


namespace Mirasvit\Search\Block\Index\Magento\Catalog;

use Magento\Catalog\Model\CategoryFactory as CatalogCategoryFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Search\Block\Index\Base;

class Category extends Base
{
    /**
     * Category constructor.
     *
     * @param CatalogCategoryFactory $categoryFactory
     * @param ObjectManagerInterface $objectManager
     * @param Context                $context
     * @param array                  $data
     */
    public function __construct(
        CatalogCategoryFactory $categoryFactory,
        ObjectManagerInterface $objectManager,
        Context $context,
        array $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->categoryFactory = $categoryFactory;

        parent::__construct($context, $objectManager, $data);
    }

    /**
     * List of parent categories
     *
     * @param int $categoryId
     * @return array
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
