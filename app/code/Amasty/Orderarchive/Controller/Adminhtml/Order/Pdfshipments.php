<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Controller\Adminhtml\Order;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Pdfshipments extends \Magento\Sales\Controller\Adminhtml\Order\Pdfshipments
{
    /**
     *
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl()?: 'amastyorderarchive/*/';
    }
}
