<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Model\ResourceModel\Report;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Points extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const FLAG_CODE = 'report_points';

    /**
     * @var array
     */
    protected $ruleTypes = [];

    /**
     * @var string
     */
    protected $pointsTable = 'mst_rewards_transaction';

    /**
     * @var \Magento\Reports\Model\FlagFactory
     */
    protected $flagFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var object
     */
    protected $resourcePrefix;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    public function __construct(
        \Magento\Reports\Model\FlagFactory $flagFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        $resourcePrefix = null
    ) {
        $this->flagFactory    = $flagFactory;
        $this->date           = $date;
        $this->storeManager   = $storeManager;
        $this->context        = $context;
        $this->resourcePrefix = $resourcePrefix;
        $this->localeDate     = $localeDate;

        parent::__construct($context, $resourcePrefix);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('mst_rewards_points_aggregated', 'points_aggregated_id');

        $this->_setResource(['read', 'write']);
    }

    /**
     * @param null $from
     *
     * @return $this
     */
    public function aggregate($from = null)
    {
        if ($from !== null) {
            $from = $this->localeDate->formatDate($from);
        }

        if ($from == null) {
            $from = new \Zend_Date(
                $this->date->gmtTimestamp(),
                null,
                $this->storeManager->getStore()->getLocaleCode()
            );

            $from->subYear(10);

            $this->_aggregatePoints($from->get(\Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT));
        } else {
            $this->_aggregatePoints($from);
        }

        $this->_refreshFlag();

        return $this;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _refreshFlag()
    {
        $flag = $this->flagFactory->create();
        $flag->setReportFlagCode(self::FLAG_CODE)
            ->unsetData()
            ->loadSelf()
            ->setLastUpdate($this->localeDate->formatDate(new \DateTime()))
            ->save()
            ;
    }

    /**
     * @param string $from
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _aggregatePoints($from)
    {
        $adapter = $this->getConnection();

        $this->collectRules();

        $aggregateTables = [
            'mst_rewards_points_aggregated_hour' => '%Y-%m-%d %H:00:00',
            //'mst_rewards_points_aggregated_hour' => '%Y-00-00 00:00:00',
        ];

        foreach ($aggregateTables as $table => $periodFormat) {
            $tableName = $this->getTable($table);

            $this->recreateTable($tableName);
            $this->addRulesToTable($tableName);

            $periodStatement = new \Zend_Db_Expr('DATE_FORMAT(points_table.created_at, "'.$periodFormat.'")');

            $customerTable = $this->getTable('customer_entity');
            $selectStoreId = 'IF ('.$customerTable.'.store_id, '.$customerTable.'.store_id, 0) as store_id';
            $select = $adapter->select()
                ->from(
                    ['points_table' => $this->getPointsTable()],
                    [
                        'period' => $periodStatement,
                        'customer_id',
                    ]
                )
                ->joinInner($customerTable, 'points_table.customer_id = '.$customerTable.'.entity_id',
                    new \Zend_Db_Expr($selectStoreId.', '.$customerTable.'.group_id as customer_group_id'))
                ->where('points_table.created_at >= ?', $from)
                ->group($periodStatement)
                ;

            $averageEarnedSql = clone $select;
            $averageEarnedSql
                ->columns(['average_points_earned' => new \Zend_Db_Expr('AVG(amount)')])
                ->where('amount > 0')
                ;

            $this->_insertOnDublicate($tableName, $averageEarnedSql, ['average_points_earned']);

            $averageSpentSql = clone $select;
            $averageSpentSql
                ->columns(['average_points_spent' => new \Zend_Db_Expr('AVG(amount)*-1')])
                ->where('amount < 0')
                ->where('code NOT LIKE "%expired%"')
                ;

            $this->_insertOnDublicate($tableName, $averageSpentSql, ['average_points_spent']);

            $totalEarnedSql = clone $select;
            $totalEarnedSql
                ->columns(['total_points_earned' => new \Zend_Db_Expr('SUM(amount)')])
                ->where('amount > 0')
                ;

            $this->_insertOnDublicate($tableName, $totalEarnedSql, ['total_points_earned']);

            $totalSpentSql = clone $select;
            $totalSpentSql
                ->columns(['total_points_spent' => new \Zend_Db_Expr('SUM(amount)*-1')])
                ->where('amount < 0')
                ->where('code NOT LIKE "%expired%"')
                ;

            $this->_insertOnDublicate($tableName, $totalSpentSql, ['total_points_spent']);

            $purchaseTable = $this->getTable('mst_rewards_purchase');
            $joinCondition = $purchaseTable.'.order_id = SUBSTRING_INDEX(points_table.code, "order_spend-", -1)';
            $totalSpentSql = clone $select;
            $totalSpentSql
                ->columns(['total_points_spent_in_money' => new \Zend_Db_Expr('SUM('.$purchaseTable.'.spend_amount)')])
                ->joinInner($purchaseTable, new \Zend_Db_Expr($joinCondition), '')
                ->where('amount < 0')
                ;

            $this->_insertOnDublicate($tableName, $totalSpentSql, ['total_points_spent_in_money']);

            $expiredSql = clone $select;
            $expiredSql
                ->columns(['total_expired_points' => new \Zend_Db_Expr('SUM(amount)')])
                ->where('is_expired = 1')
                ;

            $this->_insertOnDublicate($tableName, $expiredSql, ['total_expired_points']);

            $selectType = $this->getRuleCodeSql($from);
            $selectType->columns([
                    'total'  => new \Zend_Db_Expr('SUM(amount)'),
                    'period' => $periodStatement,
                    'customer_id',
                ])
                ->joinInner($customerTable, 'points_table.customer_id = '.$customerTable.'.entity_id',
                    new \Zend_Db_Expr($selectStoreId.', '.$customerTable.'.group_id as customer_group_id'))
                ->group('period')
            ;
            $rows = $adapter->fetchAll($selectType);
            $periods = [];
            foreach ($rows as $row) {
                $code = $row['rule_code'];
                if ($code == '') {
                    $code = 'admin_transaction';
                }
                $periods[$row['period']]['period']            = $row['period'];
                $periods[$row['period']][$code]               = $row['total'];
                $periods[$row['period']]['customer_id']       = $row['customer_id'];
                $periods[$row['period']]['customer_group_id'] = $row['customer_group_id'];
                $periods[$row['period']]['store_id']          = $row['store_id'];
            }
            foreach ($periods as $period) {
                $adapter->insertOnDuplicate(
                    $tableName,
                    $period
                );
            }
        }

        return $this;
    }

    /**
     * @param string $from
     * @return \Magento\Framework\DB\Select
     */
    private function getRuleCodeSql($from)
    {
        $adapter = $this->getConnection();

        $ruleCodeStatement = new \Zend_Db_Expr("SUBSTRING_INDEX(
                IF(
                    points_table.code REGEXP '.*import of transaction: [0-9]{1,} - .*',
                    SUBSTRING_INDEX(points_table.code, ' - ', -1),
                    points_table.code
                ),
                '-',
                1
            )");

        return $adapter->select()
            ->from(
                ['points_table' => $this->getPointsTable()],
                [
                    'rule_code' => $ruleCodeStatement,
                ]
            )
            ->where('points_table.created_at >= ?', $from)
            ->group('rule_code')
        ;
    }

    /**
     * @return void
     */
    private function collectRules()
    {
        $adapter = $this->getConnection();

        $sql = $this->getRuleCodeSql('0000-00-00'); // get rules for whole period

        $results = $adapter->query($sql);
        foreach ($results->fetchAll() as $rule) {
            if ($rule['rule_code'] == '') { // @todo add code to admin transctions
                $rule['rule_code'] = 'admin_transaction';
            }
            $this->ruleTypes[] = $rule['rule_code'];
        }
    }

    /**
     * @param string $table
     * @return void
     */
    private function addRulesToTable($table)
    {
        $adapter = $this->getConnection();
        foreach ($this->ruleTypes as $rule) {
            $adapter->addColumn($table, $rule, " int NOT NULL DEFAULT 0");
        }
    }

    /**
     * @param string $table
     * @return void
     */
    private function recreateTable($table)
    {
        $adapter = $this->getConnection();
        $adapter->dropTable($this->getTable($table));

        $table = $adapter->newTable(
            $this->getTable($table)
        )
        ->addColumn(
            'period',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Period')
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Store Id')
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Customer Id')
        ->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Customer Group Id')
        ->addColumn(
            'average_points_earned',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Average points earned')
        ->addColumn(
            'average_points_spent',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Average points spent')
        ->addColumn(
            'total_points_earned',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true, 'default' => 0],
            'Total points earned')
        ->addColumn(
            'total_points_spent',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true, 'default' => 0],
            'Total points spent')
        ->addColumn(
            'total_points_spent_in_money',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true, 'default' => 0],
            'Total points spent in money equivalent')
        ->addColumn(
            'total_expired_points',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true, 'default' => 0],
            'Total expired points')
        ->addIndex(
            $adapter->getIndexName($table, ['customer_group_id']),
            ['period', 'store_id', 'customer_id']
        );
        $adapter->createTable($table);
    }

    /**
     * @param string $tableName
     * @param string $select
     * @param array  $columns
     *
     * @return $this
     */
    protected function _insertOnDublicate($tableName, $select, $columns)
    {
        $adapter = $this->getConnection();

        $rows = $adapter->fetchAll($select);

        if (count($rows) == 0) {
            return $this;
        }
        $adapter->insertOnDuplicate(
            $tableName,
            $rows,
            $columns
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getPointsTable()
    {
        return $this->getTable($this->pointsTable);
    }
}
