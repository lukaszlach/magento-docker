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


namespace Mirasvit\SearchAutocomplete\Model\App\FrontController;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Mirasvit\SearchAutocomplete\Model\Result;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Plugin for processing ajax requests
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Plugin
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * @var ResponseHttp
     */
    protected $response;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Result                $result
     * @param ResponseHttp          $response
     * @param CacheInterface        $cache
     * @param EventManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Result $result,
        ResponseHttp $response,
        CacheInterface $cache,
        EventManagerInterface $eventManager,
        StoreManagerInterface $storeManager
    ) {
        $this->result = $result;
        $this->response = $response;
        $this->cache = $cache;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
    }

    /**
     * Call method around dispatch frontend action
     *
     * @param FrontControllerInterface $subject
     * @param \Closure                 $proceed
     * @param RequestInterface         $request
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD)
     */
    public function aroundDispatch(FrontControllerInterface $subject, \Closure $proceed, RequestInterface $request)
    {
        $startTime = microtime(true);
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $startTime = $_SERVER['REQUEST_TIME_FLOAT'];
        }

        /** @var \Magento\Framework\App\Request\Http $request */

        if (strpos($request->getOriginalPathInfo(), 'searchautocomplete/ajax/suggest') !== false) {
            $this->result->init();
            $proceed($request); #require for init translations
            $request->setControllerModule('Magento_CatalogSearch');
            $request->setDispatched(true);

            $identifier = 'QUERY_' . $this->storeManager->getStore()->getId() . '_' . md5($request->getParam('q'));

            if ($result = $this->cache->load($identifier)) {
                $result = \Zend_Json::decode($result);
                $result['time'] = round(microtime(true) - $startTime, 4);
                $result['cache'] = true;
                $data = \Zend_Json::encode($result);

            } else {
                // mirasvit core event
                $this->eventManager->dispatch('core_register_urlrewrite');

                $result = $this->result->toArray();
                $result['success'] = true;
                $result['time'] = round(microtime(true) - $startTime, 4);
                $result['cache'] = false;

                $data = \Zend_Json::encode($result);

                $this->cache->save($data, $identifier, [\Magento\PageCache\Model\Cache\Type::CACHE_TAG]);
            }

            $this->response->setPublicHeaders(3600);

            return $this->response->representJson($data);
        } else {
            return $proceed($request);
        }
    }
}
