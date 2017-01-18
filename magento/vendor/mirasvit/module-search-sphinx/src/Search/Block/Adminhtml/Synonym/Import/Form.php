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


namespace Mirasvit\Search\Block\Adminhtml\Synonym\Import;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store as SystemStore;
use Mirasvit\Search\Model\Config;

class Form extends Generic
{
    /**
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     * @param SystemStore $systemStore
     * @param Config      $config
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        SystemStore $systemStore,
        Config $config
    ) {
        $this->systemStore = $systemStore;
        $this->storeManager = $context->getStoreManager();
        $this->config = $config;

        return parent::__construct($context, $registry, $formFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('base_fieldset', []);

        $fieldset->addField('file', 'select', [
            'name'     => 'file',
            'label'    => __('Dictionary'),
            'required' => true,
            'values'   => $this->getFiles()
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

        $form->setAction($this->getUrl('*/*/doImport'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Synonym files
     *
     * @return array
     */
    protected function getFiles()
    {
        $options = [];

        $path = $this->config->getSynonymDirectoryPath();

        if (file_exists($path)) {
            $dh = opendir($path);
            while (false !== ($filename = readdir($dh))) {
                if (substr($filename, 0, 1) != '.') {
                    $info = pathinfo($filename);
                    $options[$path . DIRECTORY_SEPARATOR . $filename] = $info['filename'];
                }
            }
        }

        return $options;
    }
}
