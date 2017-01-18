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



namespace Mirasvit\Core\Model;

use Magento\Framework\FlagFactory;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\UrlInterface;

class License
{
    const EDITION_EE = 'EE';
    const EDITION_CE = 'CE';

    const STATUS_ACTIVE = 'active';
    const STATUS_LOCKED = 'locked';
    const STATUS_INVALID = 'invalid';

    /**
     * @var UrlInterface
     */
    protected $urlManager;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var FlagFactory
     */
    protected $flagFactory;

    /**
     * Constructor
     *
     * @param UrlInterface        $urlManager
     * @param ModuleListInterface $moduleList
     * @param CurlFactory         $curlFactory
     * @param FlagFactory         $flagFactory
     */
    public function __construct(
        UrlInterface $urlManager,
        ModuleListInterface $moduleList,
        CurlFactory $curlFactory,
        FlagFactory $flagFactory
    ) {
        $this->urlManager = $urlManager;
        $this->moduleList = $moduleList;
        $this->curlFactory = $curlFactory;
        $this->flagFactory = $flagFactory;
    }

    /**
     * License status
     *
     * @return string
     */
    public function getStatus()
    {
        $data = $this->getFlagData();
        if (!$data) {
            $this->request();
        }

        return self::STATUS_ACTIVE;
    }

    /**
     * Send request with all required data
     *
     * @return $this
     */
    public function request()
    {
        $params = [];
        $params['v'] = 3;
        $params['d'] = $this->getDomain();
        $params['ip'] = $this->getIP();
        $params['mv'] = $this->getVersion();
        $params['me'] = $this->getEdition();
        $params['l'] = 1;
        $params['k'] = 0;
        $params['uid'] = 0;

        $result = $this->sendRequest('http://mirasvit.com/lc/check/', $params);

        $result['time'] = time();
        $this->saveFlagData($result);

        return $this;
    }

    /**
     * Save request result to flag
     *
     * @param array $data
     * @return $this
     */
    protected function saveFlagData($data)
    {
        $flag = $this->flagFactory->create(['data' => ['flag_code' => 'core']])
            ->loadSelf();

        $flag->setFlagData(base64_encode(serialize($data)))
            ->save();

        return $this;
    }

    /**
     * Return last request result
     *
     * @return array
     */
    protected function getFlagData()
    {
        $flag = $this->flagFactory->create(['data' => ['flag_code' => 'core']])
            ->loadSelf();

        if ($flag->getFlagData()) {
            $data = @unserialize(@base64_decode($flag->getFlagData()));

            if (is_array($data)) {
                return $data;
            }
        }

        return [];
    }

    /**
     * Send http request
     *
     * @param string $endpoint
     * @param array  $params
     * @return array
     */
    public function sendRequest($endpoint, $params)
    {
        $curl = $this->curlFactory->create();
        $config = ['timeout' => 10];

        $curl->setConfig($config);
        $curl->write(
            \Zend_Http_Client::POST,
            $endpoint,
            '1.1',
            [],
            http_build_query($params, '', '&')
        );
        $response = $curl->read();

        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        $response = @unserialize($response);

        if (is_array($response)) {
            return $response;
        }

        return [];
    }

    /**
     * Backend domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->urlManager->getCurrentUrl();
    }

    /**
     * Server IP
     *
     * @return string|bool
     */
    public function getIP()
    {
        return array_key_exists('SERVER_ADDR', $_SERVER)
            ? $_SERVER['SERVER_ADDR']
            : (array_key_exists('LOCAL_ADDR', $_SERVER) ? $_SERVER['LOCAL_ADDR'] : false);
    }

    /**
     * Magento edition
     *
     * @return string
     */
    public function getEdition()
    {
        return self::EDITION_CE;
    }

    /**
     * Magento version
     *
     * @return string
     */
    public function getVersion()
    {
        $module = $this->moduleList->getOne('Magento_Backend');
        if (is_array($module) && isset($module['setup_version'])) {
            return $module['setup_version'];
        }

        return '2.0.0';
    }

}