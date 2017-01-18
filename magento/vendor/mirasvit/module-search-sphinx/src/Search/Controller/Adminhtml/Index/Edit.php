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


namespace Mirasvit\Search\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Search\Controller\Adminhtml\Index as ParentIndex;

class Edit extends ParentIndex
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $model = $this->initModel();

        if ($this->getRequest()->getParam('id')) {
            if (!$model->getId()) {
                $this->messageManager->addError(__('This search index no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $data = $this->session->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(
            $model->getId() ? __('Edit Search Index "%1"', $model->getTitle()) : __('New Search Index')
        );

        return $resultPage;
    }
}
