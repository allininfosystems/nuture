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

namespace Plumrocket\ProductFilter\Plugin\Model\CatalogSearch\Layer\Category;

class ItemCollectionProvider
{

    /**
     * Data helper
     * @var Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Collection factory
     * @var Plumrocket\ProductFilter\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     * @param \Plumrocket\ProductFilter\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Plumrocket\ProductFilter\Helper\Data                                   $dataHelper
     */
    public function __construct(
        \Plumrocket\ProductFilter\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * Arount get collection
     * @param  \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider\Interceptor $provider
     * @param  Result $result
     * @param  \Magento\Catalog\Model\Category  $category
     * @return Array
     */
    public function aroundGetCollection(
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider\Interceptor $provider,
        $result,
        \Magento\Catalog\Model\Category $category
    ) {

        if ($this->_dataHelper->moduleEnabled()) {
            $collection = $this->collectionFactory->create();
            $collection->addCategoryFilter($category);
            return $collection;
        }

        return $result($category);
    }
}
