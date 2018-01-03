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


namespace Plumrocket\ProductFilter\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveAttributes implements ObserverInterface
{

    /**
     * Filter list
     * @var Magento\Catalog\Model\Layer\Category\FilterableAttributeList
     */
    protected $_filterableAttributes;

    /**
     * Data helper
     * @var Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Constructor
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $filterableAttributes
     * @param \Plumrocket\ProductFilter\Helper\Data                                    $dataHelper
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $filterableAttributes,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper
    ) {
        $this->_filterableAttributes = $filterableAttributes;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * Changing attribute values
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $this->_filterableAttributes->create();
        $collection->setItemObjectClass('Magento\Catalog\Model\ResourceModel\Eav\Attribute')
            ->setOrder('position', 'ASC');

        $activeAttrs = $this->_dataHelper->getSelectedAttributes();
        $collection->addFieldToFilter('attribute_code', ['in' => $activeAttrs]);

        $showEmpty = $this->_dataHelper->getConfig(
            $this->_dataHelper->getConfigSectionId() . '/' . \Plumrocket\ProductFilter\Helper\Data::FILTER_SHOW_EMPTY_PATH
        );

        foreach ($collection as $attribute) {
            $save = false;
            $isFilterable = $showEmpty ? 2 : 1;
            if ($attribute->getIsFilterable() != $isFilterable) {
                $attribute->setIsFilterable($isFilterable)
                    ->setIsFilterableInSearch(1);
                $save = true;
            }

            if (!$attribute->getIsFilterableInSearch()) {
                $attribute->setIsFilterableInSearch(1);
                $save = true;
            }

            if ($save) {
                $attribute->save();
            }
        }
    }
}
