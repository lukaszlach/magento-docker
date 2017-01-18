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


namespace Mirasvit\Misspell\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Suggest extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('mst_misspell_suggest', 'suggest_id');
    }

    /**
     * Custom load model only by query text (skip synonym for)
     *
     * @param AbstractModel $object
     * @param string        $value
     * @return $this
     */
    public function loadByQuery(AbstractModel $object, $value)
    {
        $select = $this->getConnection()->select()->from($this->getMainTable())
            ->where('query = ?', $value)
            ->limit(1);

        $data = $this->getConnection()->fetchRow($select);

        if ($data) {
            $object->setData($data);
            $this->_afterLoad($object);
        } else {
            $object->setQuery($value);
        }

        return $this;
    }

    /**
     * Loading string as a value or regular numeric
     *
     * @param AbstractModel $object
     * @param int|string    $value
     * @param null|string   $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        if (is_numeric($value)) {
            return parent::load($object, $value);
        } else {
            $this->loadByQuery($object, $value);
        }
        return $this;
    }
}
