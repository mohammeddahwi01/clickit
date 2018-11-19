<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Cron;

/**
 * custom cron actions
 */
class Archiving
{
    /**
     * @var \Amasty\Orderarchive\Helper\Data
     */
    protected $helper;

    /**
     * @var \Amasty\Orderarchive\Helper\Email\Data
     */
    private $emailHelper;

    /**
     * @var \Amasty\Orderarchive\Api\ArchiveProcessorInterface
     */
    private $orderProcessor;

    /**
     * Archiving constructor.
     * @param \Amasty\Orderarchive\Helper\Data $helper
     * @param \Amasty\Orderarchive\Helper\Email\Data $emailHelper
     * @param \Amasty\Orderarchive\Api\ArchiveProcessorInterface $orderProcessor
     */
    public function __construct(
        \Amasty\Orderarchive\Helper\Data $helper,
        \Amasty\Orderarchive\Helper\Email\Data $emailHelper,
        \Amasty\Orderarchive\Api\ArchiveProcessorInterface $orderProcessor
    ) {
        $this->helper = $helper;
        $this->emailHelper = $emailHelper;
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * @return void
     */
    public function execute()
    {
        if ($this->helper->isModuleOn()) {
            $result = $this->orderProcessor->moveAllToArchive();

            if (is_array($result) && array_key_exists('order', $result) && (count($result['order']) > 0)) {
                $this->emailHelper->orderArchivedAfter($result);
            }

            return $result;
        }
    }
}
