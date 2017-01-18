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


namespace Mirasvit\Search\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mirasvit\Search\Model\Config;

class Index extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('mst_search_index', 'index_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $reindexRequired = false;

        if ($object->hasData('attributes') && is_array($object->getData('attributes'))) {
            $serialized = serialize($object->getData('attributes'));
            if ($object->getData('attributes_serialized') != $serialized) {
                $reindexRequired = true;
            }
            $object->setData('attributes_serialized', $serialized);
        }

        if ($object->hasData('properties') && is_array($object->getData('properties'))) {
            $serialized = serialize($object->getData('properties'));

            if ($object->getData('properties_serialized') != $serialized) {
                $reindexRequired = true;
            }
            $object->setData('properties_serialized', $serialized);
        }

        if ($reindexRequired && !$object->dataHasChangedFor('status')) {
            $object->setStatus(Config::INDEX_STATUS_INVALID);
        }

        $object->setCode($object->getCode());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $properties = unserialize($object->getData('properties_serialized'));
        if (is_array($properties)) {
            $object->setData('properties', $properties);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     * @param string        $value
     * @return $this
     */
    protected function loadByCode($object, $value)
    {
        $select = $this->getConnection()->select()->from($this->getMainTable())
            ->where('code = ?', $value, $value)
            ->limit(1);

        $data = $this->getConnection()->fetchRow($select);

        if ($data) {
            $object->setData($data);
            $this->_afterLoad($object);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        if (is_numeric($value)) {
            return parent::load($object, $value);
        } else {
            $this->loadByCode($object, $value);
        }

        return $this;
    }
}
