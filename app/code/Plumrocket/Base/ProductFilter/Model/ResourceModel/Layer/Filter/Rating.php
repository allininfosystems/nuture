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

class Rating extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and define main table name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('review_entity_summary', 'entity_pk_value');
    }

    /**
     * Apply rating filter to product collection
     *
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @param int $from
     * @param int $to
     * @return $this
     */
    public function applyFilterToCollection(\Magento\Catalog\Model\Layer\Filter\FilterInterface $filter, $values)
    {
        $select = $filter->getLayer()->getProductCollection()->getSelect();

        $select->joinLeft(
            ['rova'=> $this->getMainTable()],
            'e.entity_id = rova.entity_pk_value AND rova.store_id = ' . $filter->getStoreId(),
            ['rating_summary']
        );

        $conditions = [];
        foreach ($values as $value) {
            $value = (int) $value;
            if ($value == 0) {
                $conditions[] = "rova.rating_summary IS NULL";
            } else {
                $conditions[] = "rova.rating_summary >= '" . $value . "'";
            }
        }

        $select->where(implode(' OR ', $conditions));

        return $this;
    }

    /**
     * Retrieve array with rating status data of products
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

        $select->joinLeft(
            ['rating_table' => $this->getMainTable()],
            'e.entity_id = rating_table.entity_pk_value AND rating_table.store_id = "' . $filter->getStoreId() . '"',
            ['rating_summary']
        );

        $select->columns([ 'count' => new \Zend_Db_Expr("COUNT(e.entity_id)")]);
        $select->columns(
            [ 'total' => new \Zend_Db_Expr(
                "(
                CASE
                    WHEN `rating_table`.`rating_summary` >= 1 && `rating_table`.`rating_summary` < 30 THEN '1'
                    WHEN `rating_table`.`rating_summary` >= 30 && `rating_table`.`rating_summary` < 50 THEN '2'
                    WHEN `rating_table`.`rating_summary` >= 50 && `rating_table`.`rating_summary` < 70 THEN  '3'
                    WHEN `rating_table`.`rating_summary` >= 70 && `rating_table`.`rating_summary` < 90 THEN  '4'
                    WHEN `rating_table`.`rating_summary` >= 90 && `rating_table`.`rating_summary` < 1000 THEN  '5'
                    ELSE 0
                END)"
            )
            ]
        );

        $select->group('total');
        $select->order('total DESC');

        return $connection->fetchAll($select);
    }
}
