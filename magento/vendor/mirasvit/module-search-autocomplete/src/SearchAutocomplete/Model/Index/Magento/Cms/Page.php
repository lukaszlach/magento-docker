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


namespace Mirasvit\SearchAutocomplete\Model\Index\Magento\Cms;

use Magento\Cms\Helper\Page as PageHelper;
use Mirasvit\SearchAutocomplete\Model\Index\AbstractIndex;

class Page extends AbstractIndex
{
    /**
     * @var PageHelper
     */
    protected $pageHelper;

    /**
     * Constructor
     *
     * @param PageHelper $pageHelper
     */
    public function __construct(
        PageHelper $pageHelper
    ) {
        $this->pageHelper = $pageHelper;
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

        /** @var \Magento\Cms\Model\Page $page */
        foreach ($this->collection as $page) {
            $items[] = [
                'title' => $page->getTitle(),
                'url'   => $this->pageHelper->getPageUrl($page->getIdentifier()),
            ];
        }

        return $items;
    }
}
