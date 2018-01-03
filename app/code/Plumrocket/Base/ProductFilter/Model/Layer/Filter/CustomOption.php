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

class CustomOption extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{

    /**
     * Rating factory
     * @var Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter\RatingFactory
     */
    protected $_resource;

    /**
     * Data helper
     * @var Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Constructor
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param \Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter\CustomOptionFactory $customOptionFactory
     * @param \Plumrocket\ProductFilter\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter\CustomOptionFactory $customOptionFactory,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->_escaper = $escaper;
        $this->_resource = $customOptionFactory->create();
        $this->_dataHelper = $dataHelper;
        $this->_requestVar = $dataHelper->convertCustomOptionTitle($this->getLayer()->getName());
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
     * Apply rating filter to layer
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        /**
         * Filter must be string: $from-$to (percents)
         */
        $filters = $request->getParam($this->_requestVar);

        if (!$filters) {
            return $this;
        }

        $filters = explode(',', $filters);
        $_filter = implode("','", $filters);
        $this->_getResource()
            ->applyFilterToCollection(
                $this,
                $this->_dataHelper->convertToOrigin($this->_requestVar),
                $_filter
            );

        foreach ($filters as $filter) {
            $this->getLayer()
                ->getState()
                ->addFilter($this->_createItem($filter, $filter));
        }
        return $this;
    }

    /**
     * Retrieve resource model for rating
     * @return Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter\RatingFactory
     */
    protected function _getResource()
    {
        return $this->_resource;
    }

    /**
     * Retrieve filter name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        return $this->getTitle();
    }

    /**
     * Get data array for building filter of custom optios
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $options = $this->_getResource()->getCount($this);

        if (count($options) > 0) {
            foreach ($options as $option) {
                $this->itemDataBuilder->addItemData(
                    $option['title'],
                    $option['title'],
                    $option['product_count']
                );
            }
        }

        return $this->itemDataBuilder->build();
    }
}
