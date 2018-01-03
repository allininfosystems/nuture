<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter;

class StockStatus extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and define main table name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cataloginventory_stock_status', 'product_id');
    }

    /**
     * Apply stock filter to product collection
     *
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @param int $value
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function applyFilterToCollection(\Magento\Catalog\Model\Layer\Filter\FilterInterface $filter, $value)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $collection->getSelect()
            ->joinLeft(['stock'=> $this->getMainTable()], 'e.entity_id =stock.product_id', ["stock_status"])
            ->where("stock.stock_status IN (" . $value . ")")
            ->group('e.entity_id');

        return $this;
    }

    /**
     * Retrieve array with stock status data of products
     *
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    public function getCount(\Magento\Catalog\Model\Layer\Filter\FilterInterface $filter)
    {
        // Clone select from collection with filters
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        // Reset columns, order and limitation conditions
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $select->reset(\Magento\Framework\DB\Select::GROUP);

        $connection = $this->getConnection();

        $select
            ->columns(
                ['stock_status' => 'stock_status_index.stock_status',
                'count' => new \Zend_Db_Expr("COUNT(stock_status_index.product_id)")]
            )
            ->group(['stock_status_index.stock_status']);

        return $connection->fetchAll($select);
    }
}
