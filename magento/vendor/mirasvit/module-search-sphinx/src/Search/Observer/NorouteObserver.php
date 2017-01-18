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

use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlFactory;
use Mirasvit\Search\Model\Config;

class NorouteObserver implements ObserverInterface
{
    /**
     * @var array
     */
    protected $mediaTypes = [
        'jpg',
        'jpeg',
        'gif',
        'png',
        'css',
        'js',
        'ttf',
        'eot',
        'svg',
        'woff',
        'woff2',
        'ico',
        'map'
    ];

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * @param Config           $config
     * @param ManagerInterface $messageManager
     * @param UrlFactory       $urlFactory
     */
    public function __construct(
        Config $config,
        ManagerInterface $messageManager,
        UrlFactory $urlFactory
    ) {
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->urlFactory = $urlFactory;
    }

    /**
     * {@inheritdoc}
     * @param Observer $observer
     * @return bool
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isNorouteToSearchEnabled()) {
            /** @var \Magento\Framework\App\Request\Http $request */
            $request = $observer->getEvent()->getData('request');

            /** @var \Magento\Framework\App\Response\Http\Interceptor $response */
            $response = $observer->getEvent()->getData('response');

            $extension = pathinfo($request->getRequestString(), PATHINFO_EXTENSION);
            if ($response->getStatusCode() != 404
                || !$request->isGet()
                || in_array($extension, $this->mediaTypes)
            ) {
                return false;
            } else {
                $searchQuery = $this->getSearchQuery($request);

                if (!$searchQuery) {
                    return false;
                }

                $message = __('The page you requested was not found, but we have searched for relevant content.');

                $this->messageManager->addNotice($message);

                $url = $this->urlFactory->create()
                    ->addQueryParams(['q' => $searchQuery])
                    ->getUrl('catalogsearch/result');

                $response
                    ->setRedirect($url)
                    ->setStatusCode(301)
                    ->sendResponse();
            }
        }

        return true;
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @return string
     */
    protected function getSearchQuery($request)
    {
        $ignored = [
            'html',
            'php',
            'catalog',
            'catalogsearch',
            'search',
            'rma',
            'account',
            'customer',
            'helpdesk',
            'wishlist',
            'newsletter',
            'contact',
            'sendfriend',
            'product_compare',
            'review',
            'product',
            'checkout',
            'paypal',
            'sales',
            'downloadable',
            'rewards',
            'credit',
        ];
        $maxQueryLength = 128;
        $expr = '/(\W|' . implode('|', $ignored) . ')+/';
        $requestString = preg_replace($expr, ' ', $request->getRequestString());

        $terms = preg_split('/[ \- \\/_]/', $requestString);
        $terms = array_filter(array_unique($terms));

        return trim(substr(implode(' ', $terms), 0, $maxQueryLength));
    }
}
