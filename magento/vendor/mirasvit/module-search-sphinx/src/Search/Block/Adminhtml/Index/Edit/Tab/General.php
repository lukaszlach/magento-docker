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
use Mirasvit\Search\Model\Config\Source\Index as SourceIndex;

class General extends WidgetForm
{
    /**
     * @var SourceIndex
     */
    protected $sourceIndex;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * Constructor
     *
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     * @param SourceIndex $sourceIndex
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        SourceIndex $sourceIndex
    ) {
        $this->sourceIndex = $sourceIndex;
        $this->registry = $registry;
        $this->formFactory = $formFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Mirasvit\Search\Model\Index $model */
        $model = $this->registry->registry('current_model');

        $form = $this->formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('index_id', 'hidden', [
                'name' => 'id'
            ]);
        }

        $fieldset->addField('title', 'text', [
            'name'     => 'title',
            'label'    => __('Title'),
            'required' => true
        ]);

        if ($model->getId()) {
            $model->setData('index_label', $model->getIndexInstance()->toString());
            $fieldset->addField('index_label', 'label', [
                'label' => __('Index')
            ]);
        } else {
            $fieldset->addField('code', 'select', [
                'label'    => __('Index'),
                'name'     => 'code',
                'required' => true,
                'values'   => $this->sourceIndex->toOptionArray(true),
            ]);
        }

        $fieldset->addField('position', 'text', [
            'name'     => 'position',
            'label'    => __('Position'),
            'required' => true
        ]);

        $fieldset->addField('is_active', 'select', [
            'label'    => __('Status'),
            'name'     => 'is_active',
            'required' => true,
            'options'  =>
                [
                    '1' => __('Enabled'),
                    '0' => __('Disabled')
                ]
        ]);

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
