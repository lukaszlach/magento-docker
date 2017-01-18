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
use Magento\Framework\Registry;

class Attributes extends WidgetForm
{
    /**
     * Constructor
     *
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory
    ) {
        $this->registry = $registry;
        $this->formFactory = $formFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();

        $form->setHtmlIdPrefix('attributes_');

        $model = $this->registry->registry('current_model');

        if ($model->getId()) {
            $fieldset = $form->addFieldset(
                'attribute_fieldset',
                ['legend' => __('Attributes'), 'class' => 'fieldset-wide']
            );

            $fieldset->setRenderer(
                $this->getLayout()->createBlock(
                    'Mirasvit\Search\Block\Adminhtml\Index\Edit\Renderer\Attributes'
                )
                    ->setIndex($model)
            );
        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
