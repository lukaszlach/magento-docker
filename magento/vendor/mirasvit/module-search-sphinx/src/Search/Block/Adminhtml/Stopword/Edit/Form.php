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


namespace Mirasvit\Search\Block\Adminhtml\Stopword\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store as SystemStore;

class Form extends Generic
{
    /**
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     * @param SystemStore $systemStore
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        SystemStore $systemStore
    ) {

        $this->systemStore = $systemStore;
        $this->registry = $registry;
        $this->formFactory = $formFactory;
        $this->storeManager = $context->getStoreManager();

        return parent::__construct($context, $registry, $formFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $model = $this->registry->registry('current_model');

        $form = $this->formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('base_fieldset', []);

        if ($model->getId()) {
            $fieldset->addField('stopword_id', 'hidden', [
                'name' => 'id'
            ]);
        }

        $fieldset->addField('term', 'text', [
            'name'     => 'term',
            'label'    => __('Stopword'),
            'required' => true
        ]);

        if (!$this->storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', [
                'label'    => __('Store'),
                'title'    => __('Store'),
                'values'   => $this->systemStore->getStoreValuesForForm(),
                'name'     => 'store_id',
                'required' => true
            ]);
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', [
                'name'  => 'store_id',
                'value' => $this->storeManager->getStore(true)->getId()
            ]);
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
