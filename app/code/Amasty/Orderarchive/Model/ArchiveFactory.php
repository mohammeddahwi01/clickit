<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class ArchiveFactory
{
    const ORDER_ARCHIVE_NAMESPACE = 'amasty_orderachive_sales_order_grid_archive';

    const SHIPMENT_ARCHIVE_NAMESPACE = 'amasty_orderachive_sales_shipment_grid_archive';

    const INVOICE_ARCHIVE_NAMESPACE = 'amasty_orderachive_sales_invoice_grid_archive';

    const CREDITMEMO_ARCHIVE_NAMESPACE = 'amasty_orderachive_sales_creditmemo_grid_archive';

    const TYPE_NAME_OFFSET_VALUE = 3;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $archiveList;

    /**
     * ArchiveFactory constructor.
     * @param \Magento\Framework\DataObject $list
     */
    public function __construct(
        \Magento\Framework\DataObject $list
    ) {
        $this->archiveList = $list;
    }

    /**
     * @param string $type
     * @return TypeWrapper
     * @throws NoSuchEntityException
     */
    public function getArchiveModels($type)
    {
        if (!$type || !isset($this->archiveList[$type])) {
            throw new NoSuchEntityException(__('Archive is not registered'));
        }

        return $this->archiveList[$type];
    }

    /**
     * @param string $tableName
     * @return TypeWrapper
     * @throws NoSuchEntityException
     */
    public function getArchiveModelByTable($tableName)
    {
        $type = $this->getTypeFromConstant($tableName);

        if (!$type || !isset($this->archiveList[$type])) {
            throw new NoSuchEntityException(__('Archive is not registered'));
        }

        return $this->archiveList[$type];
    }

    /**
     * @param string $tableName
     * @return string
     */
    public function getTypeFromConstant($tableName)
    {
        $nameArray = explode('_', $tableName);

        if (isset($nameArray[self::TYPE_NAME_OFFSET_VALUE])) {
            return $nameArray[self::TYPE_NAME_OFFSET_VALUE];
        }

        return '';
    }
}
