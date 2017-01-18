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


namespace Mirasvit\SearchSphinx\Controller\Adminhtml\Command;

use Mirasvit\SearchSphinx\Controller\Adminhtml\Command;

class Status extends Command
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $success = false;
        $note = '';

        try {
            if ($this->engine->status($note)) {
                $message = __('Sphinx daemon running.');
                $success = true;
            } else {
                $message = __('Sphinx daemon not running.');
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        $jsonData = json_encode([
            'message' => $message,
            'note'    => $note,
            'success' => $success,
        ]);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_SearchSphinx::command_status');
    }
}
