<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Orderarchive
 */


namespace Amasty\Orderarchive\Model;

use Magento\Framework\App\ResourceConnection;
use Amasty\Orderarchive\Model\ArchiveFactory;
use Amasty\Orderarchive\Helper\Data;
use Magento\Framework\Exception\NoSuchEntityException;

abstract class ArchiveAbstract extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const ARCHIVE_ENTITY_ID = 'entity_id';

    /**
     * @var string
     */
    public $baseTable;

    /**
     * @var string
     */
    protected $archiveTable;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Amasty\Orderarchive\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var bool
     */
    protected $configMassfilter = false;

    /**
     * @var bool
     */
    protected $configDayAgo = false;

    /**
     * @var bool
     */
    protected $configStatus = false;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var array
     */
    protected $availableRemoveCollections = ['invoice', 'shipments', 'creditmemos'];

    /**
     * ArchiveAbstract constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Amasty\Orderarchive\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Amasty\Orderarchive\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->connection = $this->getConnection();
        $this->helper = $helper;
        $this->dateTime = $dateTime;
        $this->initArchiveConfig();
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     *
     * Move data by params from one table in other one
     * @param string $tableFrom
     * @param string $tableTo
     * @param array &$params
     * @return array Array displaced Orders
     */
    protected function move($tableFrom, $tableTo, array &$params)
    {
        $params = $this->prepare($tableFrom, $tableTo, $params);
        $insertFields = array_intersect(
            array_keys($this->connection->describeTable($params['tableFrom'])),
            array_keys($this->connection->describeTable($params['tableTo']))
        );
        $select = $this->getSelect($params['tableFrom'], $params['archive_ids']);

        if ($tableFrom == ArchiveFactory::ORDER_ARCHIVE_NAMESPACE
            || $tableFrom == ArchiveFactory::INVOICE_ARCHIVE_NAMESPACE
        ) {
            $select->reset(\Zend_Db_Select::COLUMNS)
                ->columns($insertFields);
        }

        $idsSelect = clone $select;
        $idsSelect->reset(\Zend_Db_Select::COLUMNS)->columns('increment_id');

        $movedIds = $this->connection->fetchCol($idsSelect);

        if ($movedIds) {
            $this->connection->exec($select->insertFromSelect($params['tableTo'], $insertFields, true));
            $this->removeFromGrid($params['tableFrom'], $params['archive_ids']);
        }

        return $movedIds;
    }

    /**
     * @param string $table
     * @return string
     */
    protected function prepareTable($table)
    {
        if (is_object($table) && !is_string($table)) {
            return $this->getMainTable();
        }

        return $this->getTable($table);
    }

    /**
     * @param $params
     * @return array
     */
    protected function prepareParams($params)
    {
        return [self::ARCHIVE_ENTITY_ID => $params];
    }

    /**
     * @param $tableName
     * @param array $params
     * @return mixed
     */
    protected function removeFromGrid($tableName, array $params)
    {
        $select = $this->getSelect($this->getTable($tableName), $params);

        return $this->connection->exec($select->deleteFromSelect($this->getTable($tableName)));
    }

    /**
     * @param $tableFrom
     * @param $tableTo
     * @param $params
     * @return array
     */
    protected function prepare($tableFrom, $tableTo, $params)
    {
        $this->baseTable = $this->prepareTable($tableFrom);
        $this->archiveTable = $this->prepareTable($tableTo);

        return [
            'archive_ids' => $this->prepareParams($params),
            'tableFrom' => $this->baseTable,
            'tableTo' => $this->archiveTable
        ];
    }

    /**
     * @param $model
     * @param $archiveIds
     * @return array
     */
    public function addToArchive($model, $archiveIds)
    {
        if (array_key_exists('source_table', $model) && array_key_exists('target_table', $model)) {
            return $this->move($model['source_table'], $model['target_table'], $archiveIds);
        }

        return [];
    }

    /**
     * @param $model
     * @param $archiveIds
     * @return array
     */
    public function removeFromArchive($model, $archiveIds)
    {
        if (array_key_exists('source_table', $model) && array_key_exists('target_table', $model)) {
            return $this->move($model['target_table'], $model['source_table'], $archiveIds);
        }

        return [];
    }

    /**
     * @param $tableName
     * @param array $params
     * @return \Magento\Framework\DB\Select
     */
    protected function getSelect($tableName, array $params)
    {
        $select = $this->connection->select()->from($tableName);

        if (isset($params[self::ARCHIVE_ENTITY_ID])) {
            $select->where($this->getOrderIdCondition($params));
        }

        return $select;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function getOrderIdCondition(array $params)
    {
        $condition = $this->connection->quoteInto(' `order_id` IN (?)', $params[self::ARCHIVE_ENTITY_ID]);

        return $condition;
    }

    /**
     * @param $selectedIds
     * @return array|mixed
     */
    public function removePermanently($selectedIds)
    {
        $res = [];
        $params = $this->prepareParams($selectedIds);
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(key($params), current($params), 'in')->create();
        $orders = $this->orderRepository->getList($searchCriteria);

        if ($orders->getSize()) {
            foreach ($orders as $item) {

                try {
                    $quote = $this->quoteRepository->get($item->getQuoteId());
                    if ($quote->getId()) {
                        $this->quoteRepository->delete($quote);
                    }
                } catch (NoSuchEntityException $e) {
                    //skip if quote wil be deleted (for some Modules, which deleted expired quote by cron)
                }

                $res['order'][] = $item->getId();
                $this->deleteAllForOrder($item, $res);
                $item->delete();
            }

            $this->removeFromGrid(\Amasty\Orderarchive\Model\ArchiveFactory::ORDER_ARCHIVE_NAMESPACE, $params);

            return $res;
        }

        return [];
    }

    /**
     * @param $order
     * @param $result
     * @return mixed
     */
    protected function deleteAllForOrder($order, &$result)
    {
        if ($order->getId()) {
            foreach ($this->availableRemoveCollections as $type) {
                $collectionName = 'get' . ucfirst($type) . 'Collection';
                $collection = $order->$collectionName();

                if ($collection->getSize()) {
                    foreach ($collection as $item) {
                        $result[$type][] = $item->getId();
                        $item->delete();
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return void
     */
    protected function initArchiveConfig()
    {
        $this->configDayAgo =
            $this->helper->getConfigValueByPath(Data::CONFIG_PATH_GENERAL_DAY_AGO);
        $this->configMassfilter =
            $this->helper->getConfigValueByPath(Data::CONFIG_PATH_GENERAL_ENABLE_MASSFILTER);
        $this->configStatus =
            $this->helper->getConfigValueByPath(Data::CONFIG_PATH_GENERAL_STATUS);
    }
}
