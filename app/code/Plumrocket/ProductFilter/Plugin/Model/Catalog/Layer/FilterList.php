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

namespace Plumrocket\ProductFilter\Plugin\Model\Catalog\Layer;

class FilterList
{

    /**
     * Data helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $dataHelper;

    /**
     * Resource connectio
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Factory of \Plumrocket\ProductFilter\Model\Layer\Filter\StockStatus
     * @var \Plumrocket\ProductFilter\Model\Layer\Filter\StockStatusFactory
     */
    protected $stockStatusFactory;

    /**
     * Factory of \Plumrocket\ProductFilter\Model\Layer\Filter\Rating
     * @var \Plumrocket\ProductFilter\Model\Layer\Filter\RatingFactory
     */
    protected $ratingFactory;

    /**
     * Factory of \Plumrocket\ProductFilter\Model\Layer\Filter\CustomOption
     * @var \Plumrocket\ProductFilter\Model\Layer\Filter\CustomOptionFactory
     */
    protected $customOptionFactory;

    /**
     * FilterList constructor.
     *
     * @param \Plumrocket\ProductFilter\Helper\Data                            $dataHelper
     * @param \Magento\Framework\App\ResourceConnection                        $resourceConnection
     * @param \Plumrocket\ProductFilter\Model\Layer\Filter\StockStatusFactory  $stockStatusFactory
     * @param \Plumrocket\ProductFilter\Model\Layer\Filter\RatingFactory       $ratingFactory
     * @param \Plumrocket\ProductFilter\Model\Layer\Filter\CustomOptionFactory $customOptionFactory
     */
    public function __construct(
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Plumrocket\ProductFilter\Model\Layer\Filter\StockStatusFactory $stockStatusFactory,
        \Plumrocket\ProductFilter\Model\Layer\Filter\RatingFactory $ratingFactory,
        \Plumrocket\ProductFilter\Model\Layer\Filter\CustomOptionFactory $customOptionFactory
    ) {
        $this->dataHelper           = $dataHelper;
        $this->resourceConnection   = $resourceConnection;
        $this->stockStatusFactory   = $stockStatusFactory;
        $this->ratingFactory        = $ratingFactory;
        $this->customOptionFactory  = $customOptionFactory;
    }

    /**
     * Arount get filter
     * @param  \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList
     * @param  \Closure $result
     * @param  \Magento\Catalog\Model\Layer $layer
     * @return \Closure
     */
    public function aroundGetFilters(
        \Magento\Catalog\Model\Layer\FilterList\Interceptor $filterList,
        $result,
        \Magento\Catalog\Model\Layer $layer
    ) {

        if (!$this->dataHelper->moduleEnabled()) {
            return $result($layer);
        }

        $filters = $result($layer);

        //This condition must be first.
        //Remove first element from array, because category filter is first
        if (!$this->dataHelper->isCategoryFilterEnabled()) {
            unset($filters[0]);
        }

        if ($this->dataHelper->isStockFilterEnabled()) {
            $filters[] = $this->stockStatusFactory->create(
                ['layer' => $layer]
            );
        }

        if ($this->dataHelper->isRatingFilterEnabled()) {
            $filters[] = $this->ratingFactory->create(
                ['layer' => $layer]
            );
        }

        if ($customOptions = $this->dataHelper->getSelectedCustomOptions(true)) {

            $options = $this->_getOptions($customOptions);

            foreach ($options as $option) {
                $layer->setName($option['title']);
                $filters[] = $this->customOptionFactory
                        ->create(
                            [
                                'layer' => $layer,
                                'data' => ['title' => $option['title']]
                            ]
                        );
            }
        }

        $filters = $this->_sortFilters($filters);

        return $filters;
    }

    /**
     * Sort filters
     * @param  array $filters
     * @return array
     */
    protected function _sortFilters($filters)
    {
        $_selected = $this->dataHelper->getSelectedAttributeCodes(true);
        $_selectedCustomOptions = $this->dataHelper->getSelectedCustomOptions();
        $_selected = array_merge($_selected, $_selectedCustomOptions);

        $_filters = [];
        foreach ($filters as $filter) {
            $_code ='';
            $isCustomOption = false;

            if ($filter instanceof \Magento\Catalog\Model\Layer\Filter\Category
                || $filter instanceof \Magento\CatalogSearch\Model\Layer\Filter\Category
            ) {
                $_code = 'category';
            } elseif ($filter->getData('attribute_model')) {
                $_code = $filter->getAttributeModel()->getAttributeCode();
            } elseif ($filter->getLayer()->getCode()) {
                $_code = $filter->getLayer()->getCode();
            } elseif ($filter instanceof \Plumrocket\ProductFilter\Model\Layer\Filter\CustomOption) {
                $isCustomOption = true;
                $_code = $filter->getName();

            } else {
                $_code = $filter->getRequestVar();
            }

            //Custom options display in the end of filter
            if (array_search($_code, $_selected) !== false) {
                $position = !$isCustomOption ?
                    array_search($_code, $_selected)
                    : array_search($_code, $_selected) + 100;
                $_filters[ $position ] = $filter->setPfAttributeCode($_code);
            }

        }

        ksort($_filters);
        return $_filters;
    }

    /**
     * Retrieve customizable options
     * @param  array $customOptions
     * @return array
     */
    protected function _getOptions($customOptions)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
                ->from(
                    ['option_title' => $this->resourceConnection->getTableName('catalog_product_option_title')]
                )
                ->columns(
                    ['option_ids' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT option_title.option_id SEPARATOR ',')")]
                )
                ->group('title')
                ->where('title IN ("' . implode('","', $customOptions) . '")');

        return $connection->fetchAll($select);
    }
}
