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
 * @package   mirasvit/module-core
 * @version   1.2.11
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Core\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Url;
use Mirasvit\Core\Api\UrlRewriteHelperInterface;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var UrlRewriteHelperInterface
     */
    protected $urlRewrite;


    /**
     * Router constructor.
     *
     * @param ActionFactory             $actionFactory
     * @param EventManagerInterface     $eventManager
     * @param UrlRewriteHelperInterface $urlRewrite
     */
    public function __construct(
        ActionFactory $actionFactory,
        EventManagerInterface $eventManager,
        UrlRewriteHelperInterface $urlRewrite
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->urlRewrite = $urlRewrite;
    }

    /**
     * {@inheritdoc}
     */
    public function match(RequestInterface $request)
    {
        /** @var \Magento\Framework\App\Request\Http $request */

        $identifier = trim($request->getPathInfo(), '/');

        $this->eventManager->dispatch(
            'core_controller_router_match_before',
            [
                'router'    => $this,
                'condition' => new DataObject(['identifier' => $identifier, 'continue' => true])
            ]
        );

        $pathInfo = $request->getPathInfo();

        $result = $this->urlRewrite->match($pathInfo);

        if ($result) {
            $params = [];
            if ($result->getEntityId()) {
                $params['id'] = $result->getEntityId();
            }
            $params = array_merge($params, $result->getActionParams());

            $request
                ->setModuleName($result->getModuleName())
                ->setControllerName($result->getControllerName())
                ->setActionName($result->getActionName())
                ->setParams($params)
                ->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
            );
        }

        return false;
    }
}
