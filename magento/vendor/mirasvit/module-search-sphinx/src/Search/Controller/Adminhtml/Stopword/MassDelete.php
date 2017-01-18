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


namespace Mirasvit\Search\Controller\Adminhtml\Stopword;

use Mirasvit\Search\Controller\Adminhtml\Stopword;

class MassDelete extends Stopword
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $stopwordIds = $this->getRequest()->getParam('stopword');
        if (!is_array($stopwordIds) || empty($stopwordIds)) {
            $this->messageManager->addError(__('Please select stopword(s).'));
        } else {
            try {
                foreach ($stopwordIds as $id) {
                    $this->stopwordFactory->create()->load($id)->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($stopwordIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
