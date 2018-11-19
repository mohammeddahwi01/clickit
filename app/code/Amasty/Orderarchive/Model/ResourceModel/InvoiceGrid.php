<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Model\ResourceModel;

class InvoiceGrid extends \Amasty\Orderarchive\Model\ArchiveAbstract
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\Orderarchive\Model\ArchiveFactory::INVOICE_ARCHIVE_NAMESPACE,
            \Amasty\Orderarchive\Model\ArchiveAbstract::ARCHIVE_ENTITY_ID
        );
    }
}
