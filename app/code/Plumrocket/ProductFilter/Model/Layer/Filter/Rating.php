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


class Rating extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{

    const FILTER_REQUEST_VAR = 'rating';

   /**
     * Rating factory
     * @var Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter\RatingFactory
     */
    protected $_resource;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param CategoryFactory $categoryDataProviderFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Plumrocket\ProductFilter\Model\ResourceModel\Layer\Filter\RatingFactory $ratingFactory,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->_escaper = $escaper;
        $this->_resource = $ratingFactory->create();
        $this->_requestVar = self::FILTER_REQUEST_VAR;
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
        $filters = explode(',', $request->getParam($this->_requestVar));

        $values = [];
        foreach ($filters as $filter) {
            if ($filter === null) {
                return $this;
            }

            if (!is_numeric($filter) || $filter < 0 || $filter > 5) {
                continue;
            }

            switch ($filter) {
                case 1 :
                    $values[] = 1;
                    break;
                case 2 :
                    $values[] = 30;
                    break;
                case 3 :
                    $values[] = 50;
                    break;
                case 4 :
                    $values[] = 70;
                    break;
                case 5 :
                    $values[] = 90;
                    break;
                default :
                    $values[] = 0;
            }
        }

        if (!count($values)) {
            return $this;
        }

        $this->_getResource()->applyFilterToCollection($this, $values);

        foreach ($values as $value) {
            $this->getLayer()
                ->getState()
                ->addFilter($this->_createItem($filter, $value))
                ->setIsActive(true);
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
        return __('Rating');
    }

    /**
     * Retrieve is radio type
     * @return boolean
     */
    public function getIsRadio()
    {
        return true;
    }

    /**
     * Retrieve code
     * @return string
     */
    public function getCode()
    {
        return \Plumrocket\ProductFilter\Model\Config\Source\AdditionalFilters::FILTER_RATING;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    protected function _getItemsData()
    {
        $ratings = $this->_getResource()->getCount($this);

        if (count($ratings) > 1) {

            $count = 0;

            foreach ($ratings as $rating) {

                $count = ($rating['rating_summary'] === null) ? $rating['count'] : $count + $rating['count'];

                $this->itemDataBuilder->addItemData(
                        $rating['total'],
                        $rating['total'],
                        $count
                    );
            }
        }

        return $this->itemDataBuilder->build();
    }

}
