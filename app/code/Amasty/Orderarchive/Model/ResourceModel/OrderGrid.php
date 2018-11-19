<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Model\ResourceModel;

use \Amasty\Orderarchive\Model\OrderProcessor;

class OrderGrid extends \Amasty\Orderarchive\Model\ArchiveAbstract
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\Orderarchive\Model\ArchiveFactory::ORDER_ARCHIVE_NAMESPACE,
            \Amasty\Orderarchive\Model\ArchiveAbstract::ARCHIVE_ENTITY_ID
        );
    }

    /**
     * @param string $tableName
     * @param array $params
     * @return \Magento\Framework\DB\Select
     */
    protected function getSelect($tableName, array $params)
    {
        $select = $this->connection->select()->from($this->getTable($tableName));

        if (array_key_exists(self::ARCHIVE_ENTITY_ID, $params) && !empty($params[self::ARCHIVE_ENTITY_ID])) {
            $select->where($this->connection->quoteIdentifier(key($params)). " IN (?)", current($params));
        }

        if (($tableName == $this->baseTable)) {

            if ($this->configStatus) {
                $select->where($this->getOrderStatusCondition());
            }

            if ($this->configDayAgo) {
                $select->where($this->getDayCondition());
            }

        }

        return $select;
    }

    /**
     * @param string $method
     * @param string $gridTable
     *
     * @return array
     */
    public function getAffectedOrderIds($method, $gridTable)
    {
        $this->baseTable = $gridTable;
        if ($method == OrderProcessor::REMOVE_FROM_ARCHIVE_METHOD_CODE) {
            $select = $this->getSelect($this->getMainTable(), []);
        } else {
            $select = $this->getSelect($gridTable, []);
        }
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('entity_id');

        return $this->connection->fetchCol($select);
    }

    /**
     * @return string
     */
    protected function getDayCondition()
    {
        $timestamp = $this->dateTime->timestamp(sprintf('- %d day', $this->configDayAgo));

        $condition =
            $this->connection->quoteInto('`created_at` < ? ', $this->dateTime->date('Y-m-d 23:59:59', $timestamp));

        return $condition;
    }

    /**
     * @return string
     */
    protected function getOrderStatusCondition()
    {
        $condition = $this->connection->quoteInto(' `status` IN (?)', explode(',', $this->configStatus));

        return $condition;
    }

    /**
     * @return array
     */
    public function getAllOrdersIds()
    {
        return $this->getConnection()
            ->select()
            ->from(
                $this->getTable($this->_mainTable),
                \Amasty\Orderarchive\Model\ArchiveAbstract::ARCHIVE_ENTITY_ID
            )
            ->query()
            ->fetchAll(\Zend_Db::FETCH_COLUMN);
    }

    /**
     * Is order moved to Archive
     *
     * @param int $id
     *
     * @return bool
     */
    public function isArchived($id)
    {
        $select = $this->connection->select()->from($this->getMainTable());
        $select->where('entity_id = ?', $id);

        return (bool)$this->connection->fetchOne($select);
    }
}
