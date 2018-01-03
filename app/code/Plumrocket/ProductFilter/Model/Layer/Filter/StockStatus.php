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

namespace Plumrocket\ProductFilter\Model\Layer\Filter;

class StockStatus extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{

    const FILTER_REQUEST_VAR = 'stock_status';

    /**
     * Resource model
     * @var Plumrocket\ProductFilter\Model\ResourceModel\Layer\FilterFactory
     */
    protected $_resource;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param  \Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter\StockStatusFactory $stockStatusFactory
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter\StockStatusFactory $stockStatusFactory,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->_requestVar = self::FILTER_REQUEST_VAR;
        $this->_resource = $stockStatusFactory->create();
    }

    /**
     * Get filter value for reset current filter state
     *
     * @return mixed|null
     */
    public function getResetValue()
    {
        return $this->dataProvider->getResetValue();
    }

    /**
     * Is radio type
     * @return boolean
     */
    public function getIsRadio()
    {
        return true;
    }

    /**
     * Apply stock status filter to layer
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $value = $request->getParam($this->_requestVar);

        if (null !== $value) {
            $this->_getResource()->applyFilterToCollection($this, $value);

            foreach (explode(',', $value) as $filter) {
                $this->getLayer()
                    ->getState()
                    ->addFilter($this->_createItem(
                        $this->_getStockStatusByValue($filter),
                        $this->_getStockStatusByValue($filter)
                    )
                );
            }
/*            $this->getLayer()
                ->getState()
                ->addFilter(
                    $this->_createItem(
                        $this->getName(),
                        $this->_getStockStatusByValue($value)
                    )
                );*/

        }

        $this->setItems([]);
        return $this;
    }

    /**
     * Get filter name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        return __('Stock Status');
    }

    /**
     * Retrieve stock status
     * @return string
     */
    public function getCode()
    {
        return \Plumrocket\ProductFilter\Model\Config\Source\AdditionalFilters::FILTER_STOCK_STATUS;
    }

    /**
     * Retrieve resource model
     * @return Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter\StockStatus
     */
    protected function _getResource()
    {
        return $this->_resource;
    }

    /**
     * Get data array for building stock filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {

        $stockStatusCounts = $this->_getResource()->getCount($this);

        if (count($stockStatusCounts) > 1) {
            $stockStatus = [
                \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK => 0,
                \Magento\CatalogInventory\Model\Stock::STOCK_OUT_OF_STOCK => 0
                ];
            foreach ($stockStatusCounts as $stockCount) {
                $stockStatus[$stockCount['stock_status']] = $stockCount['count'];
            }

            foreach ($stockStatus as $status => $count) {

                if ($count || !self::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS) {
                    $label = $this->_getStockStatusByValue($status);
                    $this->itemDataBuilder->addItemData(
                        $label,
                        $status,
                        $count
                    );
                }
            }
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * Retrieve stock status by value
     * @param  int $value
     * @return stinrg
     */
    protected function _getStockStatusByValue($value)
    {
        return (string)( $value == \Magento\CatalogInventory\Model\Stock::STOCK_IN_STOCK ) ? __('In Stock') : __('Out of Stock');
    }
}
