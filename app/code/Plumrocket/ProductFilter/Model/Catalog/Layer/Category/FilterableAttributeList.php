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

namespace Plumrocket\ProductFilter\Model\Catalog\Layer\Category;

class FilterableAttributeList extends \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
{

    /**
     * Data helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Get all attributes
     * @var boolean
     */
    protected $_getAll = false;

    /**
     * Category filter
     * @var boolean
     */
    protected $_categoryFilter = true;

    /**
     * Constructor
     * @param \Plumrocket\ProductFilter\Helper\Data                                    $dataHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface                               $storeManager
     */
    public function __construct(
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($collectionFactory, $storeManager);
    }

    /**
     * Overide old logic and add selected attributes
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        if (!$this->_getAll) {
            $selectedAttributes = $this->_dataHelper->getSelectedAttributeCodes($this->_categoryFilter);
            $collection->addFieldToFilter('attribute_code', ['in' => $selectedAttributes]);
        }

        return parent::_prepareAttributeCollection($collection);
    }

    /**
     * Set category filte
     * @param boolean $catFilter
     * @return $this
     */
    public function setCategoryFilter($catFilter = true)
    {
        $this->_categoryFilter = $catFilter;
        return $this;
    }

    /**
     * Get all attributes
     * @param boolean $getAll
     * @return  $this
     */
    public function setGetAll($getAll = false)
    {
        $this->_getAll = $getAll;
        return $this;
    }
}
