<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\ProductCollection\Contracts;

interface FacetResourceRegistry
{
    /**
     * @return FacetResource[]
     */
    public function getList();

    /**
     * @param $name
     * @return FacetResource
     */
    public function get($name);
}