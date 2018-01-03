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

namespace Plumrocket\ProductFilter\Model\CatalogSearch\Layer\Filter;

class Price extends \Magento\CatalogSearch\Model\Layer\Filter\Price
{

    /**
     * @var Dynamic\AlgorithmFactory
     */
    private $algorithmFactory;

    /** @var DataProvider\Price */
    private $dataProvider;

    /**
     * @param ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param Dynamic\AlgorithmFactory $algorithmFactory
     * @param DataProvider\PriceFactory $dataProviderFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $data
        );
        $this->algorithmFactory = $algorithmFactory;
        $this->dataProvider = $dataProviderFactory->create(['layer' => $this->getLayer()]);
    }


    /**
     * {@inheritdoc}
     */
    // protected function _getItemsData()
    // {
    //     $algorithm = $this->algorithmFactory->create();
    //     return $algorithm->getItemsData([], $this->dataProvider->getAdditionalRequestData());
    // }

    /**
     * Retrieve is radio type
     * @return boolean
     */
    public function getIsRadio()
    {
        return true;
    }
}
