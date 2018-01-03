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

class CustomOption extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and define main table name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_option_title', 'title');
    }

    /**
     * Apply custom option filter to product collection
     *
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @param  string $var
     * @param string $value
     * @return $this
     */
    public function applyFilterToCollection(\Magento\Catalog\Model\Layer\Filter\FilterInterface $filter, $var, $value)
    {
        $collection = $filter->getLayer()->getProductCollection();

        $select = $collection->getSelect();

        $select
            ->joinLeft(
                ['option_main_s'=> $this->getTable('catalog_product_option')],
                'e.entity_id = option_main_s.product_id'
            )
            ->joinLeft(
                ['option_title_s'=> $this->getTable('catalog_product_option_title')],
                'option_main_s.option_id = option_title_s.option_id'
            )
            ->joinLeft(
                ['type_value_s'=> $this->getTable('catalog_product_option_type_value')],
                'option_title_s.option_id = type_value_s.option_id'
            )
            ->joinLeft(
                ['type_title_s'=> $this->getTable('catalog_product_option_type_title')],
                'type_title_s.option_type_id = type_value_s.option_type_id'
            );

        $select->where('type_title_s.title IN ' . "('".  $value . "')");
        $select->where('option_title_s.title = ?', $var);
        $select->group('e.entity_id');


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
            ->joinLeft(
                ['option_main'=> $this->getTable('catalog_product_option')],
                'e.entity_id = option_main.product_id'
            )
            ->joinLeft(
                ['option_title'=> $this->getTable('catalog_product_option_title')],
                'option_main.option_id = option_title.option_id'
            )
            ->joinLeft(
                ['type_value'=> $this->getTable('catalog_product_option_type_value')],
                'option_title.option_id = type_value.option_id'
            )
            ->joinLeft(
                ['type_title'=> $this->getTable('catalog_product_option_type_title')],
                'type_title.option_type_id = type_value.option_type_id'
            );

        $select->where('option_main.product_id = e.entity_id');
        $select->where('option_title.title = ?', $filter->getName());

        $select->group('type_title.title');

        $select->columns(['product_count' => new \Zend_Db_Expr("COUNT(e.entity_id)"), 'title' => 'type_title.title']);
        return $connection->fetchAll($select);
    }
}
