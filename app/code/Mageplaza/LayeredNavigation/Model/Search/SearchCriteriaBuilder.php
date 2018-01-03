<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LayeredNavigation
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigation\Model\Search;

use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\Search\SearchCriteriaBuilder as SourceSearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Builder for SearchCriteria Service Data Object
 */
class SearchCriteriaBuilder extends SourceSearchCriteriaBuilder
{
    /**
     * @param ObjectFactory $objectFactory
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        ObjectFactory $objectFactory,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
    
        parent::__construct($objectFactory, $filterGroupBuilder, $sortOrderBuilder);
    }

    /**
     * @param $attributeCode
     *
     * @return $this
     */
    public function removeFilter($attributeCode)
    {
        $this->filterGroupBuilder->removeFilter($attributeCode);

        return $this;
    }

    /**
     * @return SearchCriteriaBuilder
     */
    public function cloneObject()
    {
        $cloneObject = clone $this;
        $cloneObject->setFilterGroupBuilder($this->filterGroupBuilder->cloneObject());

        return $cloneObject;
    }

    /**
     * @param $filterGroupBuilder
     */
    public function setFilterGroupBuilder($filterGroupBuilder)
    {
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * Return the Data type class name
     *
     * @return string
     */
    protected function _getDataObjectType()
    {
        return 'Magento\Framework\Api\Search\SearchCriteria';
    }
}
