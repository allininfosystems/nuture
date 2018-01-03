<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */


namespace Manadev\ProductCollection\Contracts;


use Manadev\ProductCollection\Contracts\ProductCollection;

interface QueryEngine
{
    /**
     * @return FilterResourceRegistry
     */
    public function getFilterResourceRegistry();

    /**
     * @return FacetResourceRegistry
     */
    public function getFacetResourceRegistry();

    public function run(ProductCollection $productCollection);
}