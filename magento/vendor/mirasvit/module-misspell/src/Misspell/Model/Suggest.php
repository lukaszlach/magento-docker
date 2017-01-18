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


namespace Mirasvit\Misspell\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\Misspell\Model\Misspell;

/**
 * @method string getQuery()
 * @method $this setQuery(string $query)
 * @method $this setSuggest(string $suggestion)
 */
class Suggest extends AbstractModel
{
    /**
     * @var \Mirasvit\Misspell\Model\Misspell
     */
    protected $misspell;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param Registry $registry
     * @param Misspell $misspell
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Misspell $misspell
    ) {
        $this->misspell = $misspell;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Misspell\Model\ResourceModel\Suggest');
    }

    /**
     * Return suggest string (from database or run spell correction)
     *
     * @return string
     */
    public function getSuggest()
    {
        if (!$this->getData('suggest')) {
            $suggestText = $this->misspell->getSuggest($this->getQuery());

            $this->setSuggest($suggestText)
                ->save();
        }

        return $this->getData('suggest');
    }
}
