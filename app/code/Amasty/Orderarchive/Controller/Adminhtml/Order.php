<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Controller\Adminhtml;

abstract class Order extends \Amasty\Orderarchive\Controller\Adminhtml\Archive
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $orderId = $this->getRequest()->getParam('order_id');

            $this->action($orderId);

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect->setRefererUrl();

        return $resultRedirect;
    }

    /**
     * @param int $orderId
     */
    abstract protected function action($orderId);
}
