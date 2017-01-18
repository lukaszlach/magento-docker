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


namespace Mirasvit\SearchAutocomplete\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Mirasvit\SearchAutocomplete\Helper\Data as DataHelper;

/**
 * @method AbstractElement getElement()
 * @method $this setElement(AbstractElement $element)
 */
class Index extends Field
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Constructor
     *
     * @param Context    $context
     * @param DataHelper $dataHelper
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;

        return parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->setTemplate('Mirasvit_SearchAutocomplete::config/form/field/index.phtml');
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_toHtml();
    }

    /**
     * Available indexes
     *
     * @return DataObject[]
     */
    public function getIndexes()
    {
        $indexes = $this->dataHelper->getAvailableIndexes();

        foreach ($indexes as $index) {
            $index->addData([
                'is_active' => $this->getValue($index, 'is_active'),
                'limit'     => $this->getValue($index, 'limit'),
                'order'     => $this->getValue($index, 'order'),
            ]);
        }

        usort($indexes, function ($a, $b) {
            return $a->getData('order') - $b->getData('order');
        });

        return $indexes;
    }

    /**
     * Index name
     *
     * @param DataObject $index
     * @return string
     */
    public function getNamePrefix($index)
    {
        return $this->getElement()->getName() . '[' . $index->getData('code') . ']';
    }

    /**
     * Item value for index
     *
     * @param DataObject $index
     * @param string     $item
     * @return string
     */
    public function getValue($index, $item)
    {
        if ($this->getElement()->getData('value') && is_array($this->getElement()->getData('value'))) {
            $values = $this->getElement()->getData('value');
            if (isset($values[$index->getData('code')]) && isset($values[$index->getData('code')][$item])) {
                return $values[$index->getData('code')][$item];
            }
        }

        return false;
    }
}
