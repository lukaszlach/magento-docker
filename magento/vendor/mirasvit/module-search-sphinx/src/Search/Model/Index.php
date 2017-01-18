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

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\Search\Model\Index\Pool as IndexPool;
use Magento\Framework\Model\AbstractModel;

/**
 * @method int getStatus()
 * @method $this setStatus(int $status)
 *
 * @method string getTitle()
 * @method $this setTitle(string $title)
 *
 * @method bool getIsActive()
 * @method $this setIsActive(bool $isActive)
 *
 * @method $this setAttributes(array $attributes)
 *
 * @method string getCode()
 * @method $this setCode(string $value)
 */
class Index extends AbstractModel
{
    /**
     * @var \Mirasvit\Search\Model\Index\AbstractIndex
     */
    protected $indexInstance;

    /**
     * @var IndexPool
     */
    protected $indexPool;

    /**
     * Constructor
     *
     * @param Context   $context
     * @param Registry  $registry
     * @param IndexPool $indexPool
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IndexPool $indexPool
    ) {
        $this->indexPool = $indexPool;

        $this->_init('Mirasvit\Search\Model\ResourceModel\Index');

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     * @return $this
     */
    public function afterSave()
    {
        $this->getIndexInstance()->afterModelSave();

        return parent::afterSave();
    }

    /**
     * @return \Mirasvit\Search\Model\Index\AbstractIndex
     * @throws \Exception
     */
    public function getIndexInstance()
    {
        if (!$this->indexInstance) {
            $code = $this->getData('code');

            $this->indexInstance = $this->indexPool->get($code);

            if (!$this->indexInstance) {
                throw new \Exception(__("Instance for '%1' not found", $code));
            }

            $this->indexInstance
                ->setData($this->getData())
                ->setModel($this);
        }

        return $this->indexInstance;
    }

    /**
     * Search results collection
     *
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getSearchCollection()
    {
        return $this->getIndexInstance()->getSearchCollection();
    }

    /**
     * @param string $key
     * @return bool|string
     */
    public function getProperty($key)
    {
        if (isset($this->getData('properties')[$key])) {
            return $this->getData('properties')[$key];
        }

        return false;
    }

    /**
     * @param string $key
     * @return bool|string
     */
    public function hasProperty($key)
    {
        if (is_array($this->getData('properties')) && in_array($key, $this->getData('properties'))) {
            return true;
        }

        return false;
    }

    /**
     * Unserialize index properties to data
     *
     * @return $this
     */
    public function unserializeProperties()
    {
        $properties = unserialize($this->getData('properties_serialized'));
        if (is_array($properties)) {
            $this->setData('properties', $properties);
        }

        return $this;
    }
}
