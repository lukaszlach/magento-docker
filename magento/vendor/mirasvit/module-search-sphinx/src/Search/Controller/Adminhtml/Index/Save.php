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

class Save extends ParentIndex
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $data = $this->filter($data);

            $model = $this->initModel();

            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This search index no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->addData($data);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('You saved the search index.'));

                $this->session->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->session->setFormData($data);

                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addError('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * Filter input data
     *
     * @param array $data
     * @return array
     */
    protected function filter(array $data)
    {
        $attributes = [];

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            foreach ($data['attributes']['attribute'] as $key => $attribute) {
                $weight = intval($data['attributes']['weight'][$key]);
                if (!$data['attributes']['delete'][$key]) {
                    $attributes[$attribute] = $weight;
                }
            }
        }

        $data['attributes'] = $attributes;

        return $data;
    }
}
