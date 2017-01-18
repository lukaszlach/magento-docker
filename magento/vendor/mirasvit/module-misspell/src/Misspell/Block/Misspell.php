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


namespace Mirasvit\Misspell\Block;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Misspell\Helper\Query as QueryHelper;
use Mirasvit\Misspell\Helper\Text as TextHelper;

class Misspell extends Template
{
    /**
     * @var QueryHelper
     */
    protected $query;

    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * @var TextHelper
     */
    protected $text;

    /**
     * {@inheritdoc}
     * @param Context     $context
     * @param QueryHelper $queryHelper
     * @param TextHelper  $textHelper
     * @param UrlFactory  $urlFactory
     */
    public function __construct(
        Context $context,
        QueryHelper $queryHelper,
        TextHelper $textHelper,
        UrlFactory $urlFactory
    ) {
        $this->query = $queryHelper;
        $this->urlFactory = $urlFactory;
        $this->text = $textHelper;

        parent::__construct($context);
    }

    /**
     * Return search url for query
     *
     * @param string $query
     * @return string
     */
    public function getQueryUrl($query)
    {
        return $this->urlFactory->create()
            ->addQueryParams(['q' => $query])
            ->getUrl('catalogsearch/result');
    }

    /**
     * Highlight string
     *
     * @param string $new
     * @param string $old
     * @param string $tag
     * @return string
     */
    public function highlight($new, $old, $tag = 'em')
    {
        return $this->text->htmlDiff($new, $old, $tag);
    }

    /**
     * Original search query text
     *
     * @return string
     */
    public function getOriginalQueryText()
    {
        return $this->query->getMisspellText();
    }

    /**
     * Current search query text
     *
     * @return string
     */
    public function getQueryText()
    {
        return $this->query->getQueryText();
    }

    /**
     * Misspelled text
     *
     * @return string
     */
    public function getMisspellText()
    {
        return $this->query->getMisspellText();
    }

    /**
     * Fallback text (old text)
     *
     * @return string
     */
    public function getFallbackText()
    {
        return $this->query->getFallbackText();
    }
}
