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



namespace Mirasvit\Search\Block\Adminhtml;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;

class Menu extends AbstractMenu
{
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->visibleAt(['search']);

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'id'       => 'index',
            'resource' => 'Mirasvit_Search::search_index',
            'title'    => __('Search Indexes'),
            'url'      => $this->urlBuilder->getUrl('search/index'),
        ])->addItem([
            'resource' => 'Mirasvit_Search::search_synonym',
            'title'    => __('Manage Synonyms'),
            'url'      => $this->urlBuilder->getUrl('search/synonym'),
        ])->addItem([
            'resource' => 'Mirasvit_Search::search_stopword',
            'title'    => __('Manage Stopwords'),
            'url'      => $this->urlBuilder->getUrl('search/stopword'),
        ]);

        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_Search::search_config',
            'title'    => __('Settings'),
            'url'      => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/search'),
        ]);

        return $this;
    }
}