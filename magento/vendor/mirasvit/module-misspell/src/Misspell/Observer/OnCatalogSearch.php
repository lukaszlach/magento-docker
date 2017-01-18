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


namespace Mirasvit\Misspell\Observer;

use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Misspell\Helper\Query as QueryHelper;
use Mirasvit\Misspell\Model\Config;

/**
 * Class OnCatalogSearch
 */
class OnCatalogSearch implements ObserverInterface
{
    /**
     * @var QueryHelper
     */
    protected $queryHelper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * Constructor
     *
     * @param QueryHelper  $queryHelper
     * @param Config       $config
     * @param HttpResponse $response
     */
    public function __construct(
        QueryHelper $queryHelper,
        Config $config,
        HttpResponse $response
    ) {
        $this->config = $config;
        $this->queryHelper = $queryHelper;
        $this->response = $response;
    }

    /**
     * Observer for controller_action_postdispatch_catalogsearch_result_index
     *
     * @param EventObserver $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(EventObserver $observer)
    {
        if ($this->queryHelper->getNumResults() == 0 && $this->config->isMisspellEnabled()) {
            $result = $this->doSpellCorrection();
            // if spell correction return false
            if (!$result && $this->config->isFallbackEnabled()) {
                $this->doFallbackCorrection();
            }
        }
    }

    /**
     * Run spell correction
     *
     * @return bool
     */
    public function doSpellCorrection()
    {
        $queryText = $this->queryHelper->getQueryText();
        $suggestedText = $this->queryHelper->suggest($queryText);

        if ($suggestedText
            && $suggestedText != $queryText
            && $suggestedText != $this->queryHelper->getMisspellText()
        ) {

            // perform redirect
            if ($this->queryHelper->getNumResults($suggestedText)) {
                $url = $this->queryHelper->getMisspellUrl($queryText, $suggestedText);

                $this->response->setRedirect($url);

                return true;
            }
        }

        return false;
    }

    /**
     * Run fall-back correction
     *
     * @return bool
     */
    public function doFallbackCorrection()
    {
        $queryText = $this->queryHelper->getQueryText();
        $fallbackText = $this->queryHelper->fallback($queryText);

        if ($fallbackText
            && $fallbackText != $queryText
            && $fallbackText != $this->queryHelper->getFallbackText()
        ) {

            $url = $this->queryHelper->getFallbackUrl($queryText, $fallbackText);

            $this->response->setRedirect($url);

            return true;
        }

        return false;
    }
}
