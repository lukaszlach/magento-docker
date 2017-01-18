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


namespace Mirasvit\Search\Block\Adminhtml\Index\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

class Tabs extends WidgetTabs
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param Registry         $registry
     * @param Context          $context
     * @param EncoderInterface $jsonEncoder
     * @param Session          $authSession
     */
    public function __construct(
        Registry $registry,
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession
    ) {
        $this->registry = $registry;

        return parent::__construct($context, $jsonEncoder, $authSession);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('searchindex_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Search Index Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $generalBlock = '\Mirasvit\Search\Block\Adminhtml\Index\Edit\Tab\General';
        $attributesBlock = '\Mirasvit\Search\Block\Adminhtml\Index\Edit\Tab\Attributes';
        $propertiesBlock = '\Mirasvit\Search\Block\Adminhtml\Index\Edit\Tab\Properties';

        $this->addTab('general', [
            'label'   => __('General Information'),
            'title'   => __('General Information'),
            'content' => $this->getLayout()->createBlock($generalBlock)->toHtml(),
        ]);

        if ($this->getModel()->getId()) {
            $this->addTab('attributes', [
                'label'   => __('Searchable Attributes'),
                'title'   => __('Searchable Attributes'),
                'content' => $this->getLayout()->createBlock($attributesBlock)->toHtml(),
            ]);

            if (count($this->getModel()->getIndexInstance()->getFieldsets())) {
                $this->addTab('properties', [
                    'label'   => __('Additional Options'),
                    'title'   => __('Additional Options'),
                    'content' => $this->getLayout()->createBlock($propertiesBlock)->toHtml(),
                ]);
            }
        }

        return parent::_beforeToHtml();
    }

    /**
     * Current index model
     *
     * @return \Mirasvit\Search\Model\Index
     */
    protected function getModel()
    {
        return $this->registry->registry('current_model');
    }
}
