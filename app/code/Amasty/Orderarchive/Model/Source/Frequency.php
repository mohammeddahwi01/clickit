<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */

namespace Amasty\Orderarchive\Model\Source;

class Frequency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var string
     */
    protected static $_options;

    const CRON_HOURLY          = 'H';
    const CRON_TO_TIME_PER_DAY = '2TD';
    const CRON_DAILY           = 'D';
    const CRON_WEEKLY          = 'W';
    const CRON_MONTHLY         = 'M';
    const CRON_CUSTOM          = 'CUS';

    /**
     * @var \Amasty\Orderarchive\Helper\Data
     */
    protected $helper;

    /**
     * Frequency constructor.
     * @param \Amasty\Orderarchive\Helper\Data $helper
     */
    public function __construct(
        \Amasty\Orderarchive\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @return array|string
     */
    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = [
                [
                    'label' => __('Hourly'),
                    'value' => self::CRON_HOURLY,
                ],
                [
                    'label' => __('Two Times Per Day'),
                    'value' => self::CRON_TO_TIME_PER_DAY,
                ],
                [
                    'label' => __('Daily'),
                    'value' => self::CRON_DAILY,
                ],
                [
                    'label' => __('Weekly'),
                    'value' => self::CRON_WEEKLY,
                ],
                [
                    'label' => __('Monthly'),
                    'value' => self::CRON_MONTHLY,
                ],
            ];
        }

        return self::$_options;
    }

    /**
     * get shedule
     * @param string $frequency
     * @return string
     */
    public static function generateCronSchedule($frequency)
    {
        switch ($frequency) {
            case self::CRON_HOURLY:
                $cronExpString = "0 * * * * *";
                break;
            case self::CRON_TO_TIME_PER_DAY:
                $cronExpString = '0 */12 * * *';
                break;
            case self::CRON_DAILY:
                $cronExpString = "0 0 * * * *";
                break;
            case self::CRON_WEEKLY:
                $cronExpString = "0 0 * * 0";
                break;
            case self::CRON_MONTHLY:
                $cronExpString = "0 0 1 * *";
                break;
            case self::CRON_CUSTOM:
                $cronExpString = "0 0 1 * *";
                break;
            default:
                $cronExpString = '*/10 * * * * *';
                break;
        }
        return $cronExpString;

    }
}
