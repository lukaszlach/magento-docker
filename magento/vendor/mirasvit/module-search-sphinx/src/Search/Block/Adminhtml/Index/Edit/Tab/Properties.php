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


namespace Mirasvit\Search\Block\Adminhtml\Index\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;

class Properties extends WidgetForm
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Properties constructor.
     *
     * @param Context                $context
     * @param Registry               $registry
     * @param FormFactory            $formFactory
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->objectManager = $objectManager;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();

        $form->setHtmlIdPrefix('properties_');

        $model = $this->registry->registry('current_model');

        if ($model->getId()) {
            foreach ($model->getIndexInstance()->getFieldsets() as $class) {
                $fieldset = $this->objectManager->create($class);
                $fieldset->setId($class)
                    ->setLegend('Additional Options')
                    ->setIndex($model);
                $form->addElement($fieldset);
            }
        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
