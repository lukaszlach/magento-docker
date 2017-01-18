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

use Mirasvit\Search\Controller\Adminhtml\Index as ParentIndex;

class Reindex extends ParentIndex
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $model = $this->initModel();


        if (!$model->getId()) {
            $this->messageManager->addError(__('This search index no longer exists.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $model->getIndexInstance()->reindexAll();
            $this->messageManager->addSuccess(__('Reindex successfully completed.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }
}
