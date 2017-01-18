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



namespace Mirasvit\Core\Helper;

use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mirasvit\Core\Api\CronHelperInterface;

class Cron extends AbstractHelper implements CronHelperInterface
{
    /**
     * @var ScheduleCollectionFactory
     */
    protected $scheduleCollectionFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * Cron constructor
     *
     * @param ScheduleCollectionFactory $scheduleCollectionFactory
     * @param TimezoneInterface         $timezone
     * @param MessageManagerInterface   $messageManager
     * @param Context                   $context
     */
    public function __construct(
        ScheduleCollectionFactory $scheduleCollectionFactory,
        TimezoneInterface $timezone,
        MessageManagerInterface $messageManager,
        Context $context
    ) {
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->timezone = $timezone;
        $this->messageManager = $messageManager;

        parent::__construct($context);
    }

    /**
     * Method allows to display message about not working cron job in admin panel.
     * Need call at start of adminhtml controller action.
     *
     * @param string $jobCode Cron job code (from crontab.xml).
     * @param bool   $output  By default - return cron error as adminhtml error message, otherwise - as string.
     * @param string $prefix  Additional text to cron job error message.
     * @return array [$status, $message]
     */
    public function checkCronStatus($jobCode, $output = true, $prefix = '')
    {
        if (!$this->isCronRunning($jobCode)) {
            $message = '';

            if ($prefix) {
                $message .= $prefix . ' ';
            }

            $message .= __(
                'Cron for magento is not running.'
                . ' To setup a cron job follow the <a target="_blank" href="%1">link</a>',
                'http://devdocs.magento.com/guides/v2.0/config-guide/cli/config-cli-subcommands-cron.html'
            );

            if ($output) {
                $this->messageManager->addError($message);
            } else {
                return [false, $message];
            }
        }

        return [true, ''];
    }

    /**
     * Check if cron job is exists db table and executed less 6 hours ago
     *
     * @param string $jobCode
     * @return bool
     */
    public function isCronRunning($jobCode)
    {
        $collection = $this->scheduleCollectionFactory->create();

        if ($jobCode) {
            $collection->addFieldToFilter('job_code', $jobCode);
        }

        $collection
            ->addFieldToFilter('status', 'success')
            ->setOrder('scheduled_at', 'desc')
            ->setPageSize(1);

        /** @var \Magento\Cron\Model\Schedule $job */
        $job = $collection->getFirstItem();
        if (!$job->getId()) {
            return false;
        }

        $jobTimestamp = strtotime($job->getExecutedAt()); //in store timezone
        $timestamp = $this->timezone->scopeTimeStamp();  //in store timezone

        if (abs($timestamp - $jobTimestamp) > 6 * 60 * 60) {
            return false;
        }

        return true;
    }
}
