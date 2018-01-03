<?php

namespace Plumrocket\ProductFilter\Model;

class FilterList extends \Magento\Framework\Model\AbstractModel
{

	protected $_additionalFilters;

	protected $_filterableAttributes;

	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $filterableAttributes,
        \Plumrocket\ProductFilter\Model\Config\Source\AdditionalFilters $additionalFilters,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_additionalFilters = $additionalFilters;
        $this->_filterableAttributes = $filterableAttributes;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    public function getFilters()
    {
    	$collection = $this->_filterableAttributes->create();
        $collection->setItemObjectClass('Magento\Catalog\Model\ResourceModel\Eav\Attribute')
            ->setOrder('position', 'ASC');

        $collection->addFieldToFilter(
            ['additional_table.is_filterable', 'frontend_input'],
            [
                ['gt' => 0],
                ['in' => ['select', 'price', 'multiselect']]
            ]
        );

        $collection->addFieldToFilter('attribute_code', ['neq' => 'visibility']);


        //Create simple array with attributes
        $attributes = [];
        foreach ($collection as $attr) {
            $attributes[$attr->getAttributeCode()] = $attr;
        }

        //Adding additional filters
        $additionalFilters = $this->_getAdditionalFilters();
        $attributes = array_merge($attributes, $additionalFilters);

        return $attributes;
    }


    protected function _getAdditionalFilters()
    {
    	$filters = $this->_additionalFilters->toArray();

    	$_filters = [];
    	foreach ($filters as $code => $filter) {
    		$_filters[$code] = new \Magento\Framework\DataObject(['attribute_code' => $code, 'frontend_label' => $filter]);
    	}
    	return $_filters;
    }

}