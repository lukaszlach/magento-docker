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


namespace Mirasvit\Search\Observer;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlFactory;
use Magento\Search\Model\QueryFactory;
use Mirasvit\Search\Model\Config;

class SingleResultObserver implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * @var LayerResolver
     */
    protected $layerResolver;

    /**
     * @var \Magento\Search\Model\QueryInterface
     */
    protected $query;

    /**
     * Constructor
     *
     * @param Config        $config
     * @param UrlFactory    $urlFactory
     * @param LayerResolver $layerResolver
     * @param QueryFactory  $queryFactory
     */
    public function __construct(
        Config $config,
        UrlFactory $urlFactory,
        LayerResolver $layerResolver,
        QueryFactory $queryFactory
    ) {
        $this->config = $config;
        $this->urlFactory = $urlFactory;
        $this->layerResolver = $layerResolver;
        $this->query = $queryFactory->get();
    }

    /**
     * {@inheritdoc}
     * @param Observer $observer
     * @return bool
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $observer->getEvent()->getData('request');

        // search page && feature enabled && not misspelled
        if ($request->getControllerModule() == 'Magento_CatalogSearch'
            && $this->config->isRedirectOnSingleResult()
            && !$request->getParam('o')
        ) {
            // if one result and this is product
            if ($this->query->getNumResults() == 1
                && $this->layerResolver->get()->getProductCollection()->getSize() == 1
            ) {
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->layerResolver->get()->getProductCollection()->getFirstItem();

                /** @var \Magento\Framework\App\Response\Http\Interceptor $response */
                $response = $observer->getEvent()->getData('response');

                $response
                    ->setRedirect($product->getProductUrl())
                    ->setStatusCode(301)
                    ->sendResponse();
            }
        }

        return true;
    }
}
