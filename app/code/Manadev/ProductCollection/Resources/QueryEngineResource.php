<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\ProductCollection\Resources;

use Magento\Framework\DB\Select;
use Manadev\Core\Registries\ProductAttributes;
use Manadev\ProductCollection\Configuration;
use Manadev\ProductCollection\Contracts\Facet;
use Manadev\ProductCollection\Contracts\FacetBatch;
use Manadev\ProductCollection\Contracts\FacetResource;
use Manadev\ProductCollection\Contracts\FacetResourceRegistry;
use Manadev\ProductCollection\Contracts\Filter;
use Manadev\ProductCollection\Contracts\FilterResourceRegistry;
use Manadev\ProductCollection\Contracts\QueryEngine;
use Manadev\ProductCollection\Factory;
use Manadev\ProductCollection\Query;
use Manadev\ProductCollection\Registries\FacetResources;
use Manadev\ProductCollection\Registries\FilterResources;
use Manadev\ProductCollection\Contracts\ProductCollection;
use Manadev\ProductCollection\Resources\Collections\FullTextProductCollection;

class QueryEngineResource implements QueryEngine
{
    /**
     * @var FilterResources
     */
    protected $filterResources;
    /**
     * @var FacetResources
     */
    protected $facetResources;
    /**
     * @var Configuration
     */
    protected $configuration;
    /**
     * @var Factory
     */
    protected $factory;

    public function __construct(FilterResources $filterResources, FacetResources $facetResources,
        Configuration $configuration, Factory $factory)
    {
        $this->filterResources = $filterResources;
        $this->facetResources = $facetResources;
        $this->configuration = $configuration;
        $this->factory = $factory;
    }

    /**
     * @return FilterResourceRegistry
     */
    public function getFilterResourceRegistry() {
        return $this->filterResources;
    }

    /**
     * @return FacetResourceRegistry
     */
    public function getFacetResourceRegistry() {
        return $this->facetResources;
    }

    /**
     * @param ProductCollection $productCollection
     */
    public function run(ProductCollection $productCollection) {
        /* @var FullTextProductCollection $productCollection */
        $query = $productCollection->getQuery();

        $select = clone $productCollection->getSelect();

        $this->applyFiltersToSelect($productCollection->getSelect(), $query);

        /* @var FacetBatch[] $batches */
        $batches = [];

        foreach ($query->getFacets() as $facet) {
            $resource = $this->facetResources->get($facet->getType());

            if ($this->registerForBatchCounting($batches, $resource, $facet)) {
                continue;
            }

            if ($resource->isPreparationStepNeeded()) {
                $preparationSelect = $this->applyFiltersToSelect(clone $select, $query,
                    $resource->getPreparationFilterCallback($facet));
                $resource->prepare($preparationSelect, $facet);
            }
            else {
                $preparationSelect = null;
            }

            $facetSelect = $this->applyFiltersToSelect(clone $select, $query, $resource->getFilterCallback($facet));
            $facet->setData($resource->count($facetSelect, $facet));
        }

        foreach ($batches as $batch) {
            $resource = $this->facetResources->get($batch->getType());

            if ($resource->isPreparationStepNeeded()) {
                $resource->prepare(clone $productCollection->getSelect(), $batch);
            }

            $resource->count(clone $productCollection->getSelect(), $batch);
        }

        foreach ($query->getFacets() as $facet) {
            if (!$facet->getData()) {
                continue;
            }

            $resource = $this->facetResources->get($facet->getType());
            $resource->sort($facet);
        }
    }

    /**
     * @param Select $select
     * @param Filter $filter
     * @param callable $callback
     * @return false|string
     */
    public function applyFilterToSelectRecursively(Select $select, Filter $filter, $callback = null) {
        if ($callback && !call_user_func($callback, $filter)) {
            return false;
        }

        $resource = $this->filterResources->get($filter->getType());
        return $resource->apply($select, $filter, $callback);
    }

    protected function applyFiltersToSelect(Select $select, Query $query, $callback = null) {
        if ($condition = $this->applyFilterToSelectRecursively($select, $query->getFilters(), $callback)) {
            $select->where($condition);
        }
        $sql = $select->__toString();

        return $select;
    }

    /**
     * @param FacetBatch[] $batches
     * @param FacetResource $resource
     * @param Facet $facet
     * @return bool
     */
    protected function registerForBatchCounting(&$batches, $resource, $facet) {
        if (!$this->configuration->isBatchFilterCountingEnabled()) {
            return false;
        }

        if (!($batchType = $resource->getBatchType($facet))) {
            return false;
        }

        if (!isset($batches[$batchType])) {
            $batches[$batchType] = $this->factory->createFacetBatch($batchType);
        }
        $batches[$batchType]->addFacet($facet);

        return true;
    }
}