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
 * @package   mirasvit/module-core
 * @version   1.2.11
 * @copyright Copyright (C) 2016 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Model;

use Magento\AdminNotification\Model\Feed;

class NotificationFeed extends Feed
{
    /**
     * @var string
     */
    protected $feedUrl;

    /**
     * {@inheritdoc}
     */
    public function getFeedUrl()
    {
        if ($this->feedUrl === null) {
            $this->feedUrl = 'https://mirasvit.com/blog/category/magento-2-feed/feed/';
        }

        return $this->feedUrl;
    }
}