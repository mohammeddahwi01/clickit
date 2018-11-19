<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Model;

class InvoiceGrid extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Amasty\Orderarchive\Model\ResourceModel\InvoiceGrid');
    }
}
