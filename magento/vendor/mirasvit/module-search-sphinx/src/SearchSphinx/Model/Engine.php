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


namespace Mirasvit\SearchSphinx\Model;

use Mirasvit\SearchSphinx\SphinxQL\SphinxQL;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Mirasvit\Search\Model\ResourceModel\Index\CollectionFactory as IndexCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SearchSphinx\Helper\Data as Helper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Engine
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var IndexCollectionFactory
     */
    protected $indexCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $configFilePath;

    /**
     * @var string
     */
    protected $absConfigFilePath;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var \Mirasvit\SearchSphinx\SphinxQL\Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $availableAttributes = [];

    /**
     * @param Filesystem             $fs
     * @param Config                 $config
     * @param Helper                 $helper
     * @param IndexCollectionFactory $indexCollectionFactory
     * @param StoreManagerInterface  $storeManager
     */
    public function __construct(
        Filesystem $fs,
        Config $config,
        Helper $helper,
        IndexCollectionFactory $indexCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->helper = $helper;
        $this->indexCollectionFactory = $indexCollectionFactory;
        $this->storeManager = $storeManager;

        $this->directory = $fs->getDirectoryWrite(DirectoryList::VAR_DIR);

        $this->basePath = $fs->getDirectoryRead(DirectoryList::VAR_DIR)->getRelativePath('sphinx');
        $this->configFilePath = $this->basePath . DIRECTORY_SEPARATOR . 'sphinx.conf';

        $this->absConfigFilePath = $this->directory->getAbsolutePath($this->configFilePath);

        $this->host = $this->config->getHost();
        $this->port = $this->config->getPort();

        // check all paths
        foreach ($this->config->getBinPath() as $binPath) {
            $this->searchdCommand = $binPath;
            if ($this->isAvailable()) {
                break;
            }
        }

        $this->connection = new \Mirasvit\SearchSphinx\SphinxQL\Connection();
        $this->connection->setParams([
                'host' => $this->host,
                'port' => $this->port
            ]
        );

        if (file_exists($this->absConfigFilePath . '.attr')) {
            $this->availableAttributes = json_decode(file_get_contents($this->absConfigFilePath . '.attr'), true);
        }
    }

    /**
     * @param \Mirasvit\Search\Model\Index $index
     * @param string                       $indexName
     * @param array                        $documents
     * @return void
     */
    public function saveDocuments($index, $indexName, array $documents)
    {
        foreach ($documents as $id => $document) {
            $this->getQuery()
                ->delete()
                ->from($indexName)
                ->where('id', '=', $id)
                ->execute();

            $query = $this->getQuery()
                ->insert()
                ->into($indexName)
                ->value('id', $id);

            $doc = [];

            foreach ($document as $attr => $value) {
                if (is_int($attr)) {
                    $attr = $index->getIndexInstance()->getAttributeCode($attr);
                }

                if (isset($this->availableAttributes[$indexName])
                    && !in_array($attr, $this->availableAttributes[$indexName])
                ) {
                    $attr = 'options';
                }

                if (isset($doc[$attr])) {
                    $doc[$attr] .= ' ' . $value;
                } else {
                    $doc[$attr] = $value;
                }
            }
            foreach ($doc as $attr => $value) {
                $query->value($attr, $value);
            }

            try {
                $query->execute();
            } catch (\Exception $e) {
            }

        }
    }

    /**
     * @param \Mirasvit\Search\Model\Index $index
     * @param string                       $indexName
     * @param array                        $documents
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function deleteDocuments($index, $indexName, array $documents)
    {
        if (!$this->status()) {
            $this->start();
        }

        foreach ($documents as $document) {
            $this->getQuery()
                ->delete()
                ->from($indexName)
                ->where('id', '=', $document)
                ->execute();
        }
    }

    /**
     * @return SphinxQL
     */
    public function getQuery()
    {
        $this->connection->ping();

        return new SphinxQL($this->connection);
    }

    /**
     * @param string &$output
     * @return bool
     * @throws \Exception
     */
    public function start(&$output = '')
    {
        if (!$this->isAvailable($output)) {
            return false;
        }

        $this->makeConfig();

        $command = "$this->searchdCommand --config $this->absConfigFilePath";
        $exec = $this->helper->exec($command);

        $output .= $exec['data'];

        if ($exec['status'] === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string &$output
     * @return bool
     * @throws \Exception
     */
    public function stop(&$output = '')
    {
        if (!$this->isAvailable($output)) {
            return false;
        }

        // first attempt (normal)
        $command = $this->searchdCommand . ' --config ' . $this->absConfigFilePath . ' --stopwait';
        $exec = $this->helper->exec($command);
        $output .= $exec['data'];

        // second attempt (forced)
        $find = "ps aux | grep searchd | grep $this->absConfigFilePath  | awk '{print $2}'";
        $pids = $this->helper->exec($find);
        foreach (explode(PHP_EOL, $pids['data']) as $id) {
            $command = "kill -9 $id";
            $this->helper->exec($command);
        }

        if ($exec['status'] === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string &$output
     * @return bool
     */
    public function restart(&$output = '')
    {
        if (!$this->isAvailable($output)) {
            return false;
        }

        $this->stop($output);

        return $this->start($output);
    }

    /**
     * @param string &$output
     * @return bool
     * @throws \Exception
     */
    public function status(&$output = '')
    {
        if (!$this->isAvailable($output)) {
            return false;
        }

        $output = '';

        $command = "$this->searchdCommand --config $this->absConfigFilePath --status";
        $exec = $this->helper->exec($command);

        $output .= $exec['data'] . PHP_EOL;

        $command = "ps aux | grep searchd | awk '{print $2,$9,$11,$12,$13;}'";
        $exec = $this->helper->exec($command);

        $output .= $exec['data'] . PHP_EOL;

        if (strpos($output, 'failed to connect to') !== false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string &$output
     * @return bool
     */
    public function reset(&$output = '')
    {
        $this->stop($output);

        $path = $this->directory->getAbsolutePath($this->basePath);
        $command = "rm -rf $path";
        $exec = $this->helper->exec($command);
        $output .= $exec['data'];

        return true;
    }

    /**
     * @param string &$output
     * @return bool
     * @throws \Exception
     */
    public function isAvailable(&$output = '')
    {
        $command = "$this->searchdCommand --config fake.conf 2>&1";

        $exec = $this->helper->exec($command);

        if (strpos($exec['data'], 'failed to parse config file')) {
            return true;
        } else {
            $output .= __('Searchd not found at %1', $this->searchdCommand);

            return false;
        }
    }

    /**
     * @return string Path to config file
     * @throws \Exception
     */
    public function makeConfig()
    {
        if (!$this->directory->isExist($this->basePath)) {
            //$this->directory->delete($this->basePath);
            $this->directory->create($this->basePath);
            $this->directory->changePermissions($this->basePath, 0777);
        }

        $jsonData = [];

        $sphinxData = [
            'time'          => date('d.m.Y H:i:s'),
            'host'          => $this->host,
            'port'          => $this->port,
            'fallback_port' => $this->port - 1,
            'logdir'        => $this->directory->getAbsolutePath($this->basePath),
            'sphinxdir'     => $this->directory->getAbsolutePath($this->basePath),
            'indexes'       => '',
            'localdir'      => dirname(dirname(__FILE__)),
            'custom'        => $this->config->getAdditionalSearchdConfig(),
        ];

        $sphinxTemplate = $this->config->getSphinxConfigurationTemplate();
        $indexTemplate = $this->config->getSphinxIndexConfigurationTemplate();

        /** @var \Mirasvit\Search\Model\Index $index */
        foreach ($this->indexCollectionFactory->create() as $index) {
            foreach (array_keys($this->storeManager->getStores()) as $storeId) {
                $indexName = $index->getIndexInstance()->getIndexer()->getIndexName($storeId);
                $data = [
                    'name'         => $indexName,
                    'min_word_len' => 1,
                    'path'         => $this->directory->getAbsolutePath($this->basePath) . '/' . $indexName,
                    'custom'       => $this->config->getAdditionalIndexConfig(),
                ];

                $jsonAttributes = [];
                $attributes = [];
                foreach (array_keys($index->getIndexInstance()->getAttributes(true)) as $attribute) {
                    $attributes[] = "    rt_field = $attribute";
                    $jsonAttributes[] = $attribute;

                    if (count($attributes) > 250) {
                        break;
                    }
                }

                $attributes[] = "    rt_field = options";
                $jsonAttributes[] = "options";

                $data['attributes'] = implode(PHP_EOL, $attributes);

                $sphinxData['indexes'] .= $this->helper->filterTemplate($indexTemplate, $data);

                $jsonData[$indexName] = $jsonAttributes;
            }
        }

        $config = $this->helper->filterTemplate($sphinxTemplate, $sphinxData);

        if ($this->directory->isWritable($this->basePath)) {
            $this->directory->writeFile($this->configFilePath, $config);
            $this->directory->writeFile($this->configFilePath . '.attr', json_encode($jsonData));
        } else {
            if ($this->directory->isExist($this->configFilePath)) {
                throw new \Exception(__('File %1 does not writable', $this->configFilePath));
            } else {
                throw new \Exception(__('Directory %1 does not writable', $this->basePath));
            }
        }

        return $this->directory->getAbsolutePath($this->configFilePath);
    }
}
