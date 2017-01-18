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


namespace Mirasvit\Search\Block\Options;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Search\Model\Config;

class GoogleSitelinks extends Template
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Config  $config
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Is enabled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->isGoogleSitelinksEnabled();
    }

    /**
     * Store base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->getUrl();
    }

    /**
     * Search target url
     *
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->getUrl(
            'catalogsearch/result/index',
            [
                '_query' => [
                    'q' => '{search_term_string}'
                ]
            ]
        );
    }
}
