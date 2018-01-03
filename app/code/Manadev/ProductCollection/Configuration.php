<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\ProductCollection;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Configuration
{
    const QUERY_ENGINE = 'catalog/search/engine';
    const EQUALIZED_COUNT_INTERVAL_DIVISION_LIMIT = 'catalog/layered_navigation/interval_division_limit';
    const DEFAULT_PRICE_NAVIGATION_STEP = 'catalog/layered_navigation/price_range_step';
    const MAX_NUMBER_OF_PRICE_INTERVALS = 'catalog/layered_navigation/price_range_max_intervals';
    const PRICE_RANGE_CALCULATION_METHOD = 'catalog/layered_navigation/price_range_calculation';
    const PRODUCT_COLLECTION_QUERY_LOGGING = 'mana_core/log/product_collection_queries';
    const FACET_COUNTING_QUERY_LOGGING = 'mana_core/log/facet_counting_queries';
    const BATCH_FILTER_COUNTING = 'mana_core/experimental/batch_filter_counting';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $categoryFlatState;

    public function __construct(ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState)
    {
        $this->scopeConfig = $scopeConfig;
        $this->categoryFlatState = $categoryFlatState;
    }

    public function getQueryEngine() {
        return $this->scopeConfig->getValue(static::QUERY_ENGINE);
    }

    public function areCategoriesFlat() {
        return $this->categoryFlatState->isAvailable();
    }

    public function getEqualizedCountIntervalDivisionLimit() {
        return $this->scopeConfig->getValue(static::EQUALIZED_COUNT_INTERVAL_DIVISION_LIMIT, ScopeInterface::SCOPE_STORE);
    }

    public function getDefaultPriceNavigationStep() {
        return $this->scopeConfig->getValue(static::DEFAULT_PRICE_NAVIGATION_STEP, ScopeInterface::SCOPE_STORE);
    }

    public function getMaxNumberOfPriceIntervals() {
        return $this->scopeConfig->getValue(static::MAX_NUMBER_OF_PRICE_INTERVALS, ScopeInterface::SCOPE_STORE);
    }

    public function getPriceRangeCalculationMethod() {
        return $this->scopeConfig->getValue(static::PRICE_RANGE_CALCULATION_METHOD, ScopeInterface::SCOPE_STORE);
    }

    public function isProductCollectionQueryLoggingEnabled() {
        return $this->scopeConfig->isSetFlag(static::PRODUCT_COLLECTION_QUERY_LOGGING);
    }

    public function isFacetCountingQueryLoggingEnabled() {
        return $this->scopeConfig->isSetFlag(static::FACET_COUNTING_QUERY_LOGGING);
    }

    public function isBatchFilterCountingEnabled() {
        return $this->scopeConfig->isSetFlag(static::BATCH_FILTER_COUNTING);
    }
}