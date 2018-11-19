<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Controller\Adminhtml\Order;

use Amasty\Orderarchive\Cron\Archiving;

class Index extends \Amasty\Orderarchive\Controller\Adminhtml\Archive
{

    /**
     * Orders grid
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Archive Orders'));

        return $resultPage;
    }
}
