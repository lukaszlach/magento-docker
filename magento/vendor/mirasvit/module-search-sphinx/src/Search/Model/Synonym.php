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
use Mirasvit\Search\Model\ResourceModel\Synonym\CollectionFactory;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * @method string getSynonyms()
 * @method string getTerm()
 */
class Synonym extends AbstractModel
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
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Search\Model\ResourceModel\Synonym');
    }

    /**
     * Import synonyms from file
     *
     * @param string $file
     * @param array  $storeIds
     * @return array
     */
    public function import($file, $storeIds)
    {
        $result = [
            'synonyms' => 0,
            'errors'   => []
        ];

        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        $resource = $this->getResource();
        $connection = $resource->getConnection();
        $tableName = $resource->getTable('mst_search_synonym');

        if (!file_exists($file)) {
            throw new \Exception("File $file not exists.");
        }

        $synonyms = (new YamlParser())->parse(file_get_contents($file));

        foreach ($storeIds as $store) {
            $rows = [];

            foreach ($synonyms as $synonym) {
                try {
                    $rows[] = [
                        'term'     => $synonym['term'],
                        'synonyms' => $synonym['synonyms'],
                        'store_id' => $store,
                    ];

                    if (count($rows) > 1000) {
                        $connection->insertArray($tableName, ['term', 'synonyms', 'store_id'], $rows);
                        $rows = [];
                    }

                    $result['synonyms']++;
                } catch (\Exception $e) {
                    $result['errors']++;
                }
            }

            if (count($rows) > 0) {
                $connection->insertArray($tableName, ['term', 'synonyms', 'store_id'], $rows);
            }
        }

        return $result;
    }

    /**
     * Prepare and validate synonym
     *
     * @param string $word
     * @return string
     * @throws \Exception
     */
    public function prepareWord($word)
    {
        $word = trim(strtolower($word));

        if (strlen($word) <= 1) {
            throw new \Exception(__('The length of synonym must be greater than 1.'));
        }

        if (count(explode(' ', $word)) != 1) {
            throw new \Exception(__('Synonym "%1" can contain only one word.', $word));
        }

        return $word;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $synonyms = $this->getData('synonyms');
        $synonyms = array_unique(array_filter(explode(',', $synonyms)));

        $this->setData('synonyms', implode(',', $synonyms));

        return parent::beforeSave();
    }

    /**
     * Return array of synonyms for words
     *
     * @param array $arWord
     * @return array
     */
    public function getSynonymsByWord($arWord)
    {
        $result = [];

        if (!is_array($arWord)) {
            $arWord = [$arWord];
        }

        $collection = $this->collectionFactory->create();

        foreach ($arWord as $word) {
            $collection->getSelect()
                ->orWhere('term = ?', $word);
        }

        /** @var Synonym $model */
        foreach ($collection as $model) {
            $synonyms = explode(',', $model->getSynonyms());

            foreach ($arWord as $word) {
                if ($model->getTerm() === $word) {
                    foreach ($synonyms as $synonym) {
                        $result[$word][$synonym] = $synonym;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAvailableFiles()
    {
        $files = [];

        $path = $this->config->getSynonymDirectoryPath();

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
