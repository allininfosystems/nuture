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

namespace Plumrocket\ProductFilter\Model\Catalog\Layer\Filter\Price;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Plumrocket\ProductFilter\Helper\Data as DataHelper;

class Range extends \Magento\Catalog\Model\Layer\Filter\Price\Range
{

    protected $dataHelper;

    private $registry;

    /**
     * Contructor
     * @param DataHelper           $dataHelper
     * @param Registry             $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param Resolver             $layerResolver
     */
    public function __construct(
        DataHelper $dataHelper,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        Resolver $layerResolver
    ) {
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;
        parent::__construct($registry, $scopeConfig, $layerResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceRange()
    {
        if ($this->dataHelper->moduleEnabled()) {
            $categories = $this->registry->registry('current_category_filter');
            if ($categories &&
                ($categories instanceof \Magento\Catalog\Model\ResourceModel\Category\Collection
                    || $categories instanceof \Magento\Catalog\Model\ResourceModel\Category\Flat\Collection)
            ) {
                return $categories->getFirstItem()->getFilterPriceRange();
            }
        }
        return parent::getPriceRange();
    }
}
