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


namespace Mirasvit\Search\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\Search\Model\ResourceModel\Stopword\CollectionFactory;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * @method $this setTerm(string $word)
 * @method string getTerm()
 */
class Stopword extends AbstractModel
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config            $config
     * @param Context           $context
     * @param Registry          $registry
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Config $config,
        Context $context,
        Registry $registry,
        CollectionFactory $collectionFactory
    ) {
        $this->config = $config;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Search\Model\ResourceModel\Stopword');
    }

    /**
     * Import stopwords from file
     *
     * @param string $file
     * @param array  $storeIds
     * @return array
     */
    public function import($file, $storeIds)
    {
        $result = [
            'stopwords' => 0,
            'errors'    => 0,
        ];

        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        $resource = $this->getResource();
        $connection = $resource->getConnection();
        $tableName = $resource->getTable('mst_search_stopword');

        $stopwords = (new YamlParser())->parse(file_get_contents($file));

        foreach ($storeIds as $store) {
            $rows = [];

            foreach ($stopwords as $stopword) {
                try {
                    $result['stopwords']++;

                    $rows[] = [
                        'term'     => $stopword,
                        'store_id' => $store,
                    ];

                    if (count($rows) > 1000) {
                        $connection->insertArray($tableName, ['term', 'store_id'], $rows);
                        $rows = [];
                    }
                } catch (\Exception $e) {
                    $result['errors']++;
                }
            }

            if (count($rows) > 0) {
                $connection->insertArray($tableName, ['term', 'store_id'], $rows);
            }
        }

        return $result;
    }

    /**
     * Prepare and validate stopword
     *
     * @param string $word
     * @return string
     * @throws \Exception
     */
    public function prepareWord($word)
    {
        $word = trim(strtolower($word));

        if (count(explode(' ', $word)) > 1) {
            throw new \Exception(__('Stopword "%1" can contain only one word.', $word));
        }
        if ($word === '?') {
            throw new \Exception(__('Stopword contains an invalid character: "%1".', $word));
        }

        return $word;
    }

    /**
     * Is stopword?
     *
     * @param string $word
     * @param int    $storeId
     * @return bool
     */
    public function isStopWord($word, $storeId)
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('term', $word)
            ->addFieldToFilter('store_id', $storeId);

        $result = $collection->getSize();

        return $result;
    }

    /**
     * @return array
     */
    public function getAvailableFiles()
    {
        $files = [];

        $path = $this->config->getStopwordDirectoryPath();

        if (file_exists($path)) {
            $dh = opendir($path);
            while (false !== ($filename = readdir($dh))) {
                if (substr($filename, 0, 1) != '.') {
                    $files[] = $path . DIRECTORY_SEPARATOR . $filename;
                }
            }
        }

        return $files;
    }
}
