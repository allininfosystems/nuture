<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\ProductCollection\Contracts;

interface FilterResourceRegistry
{
    /**
     * @return FilterResource[]
     */
    public function getList();

    /**
     * @param $name
     * @return FilterResource
     */
    public function get($name);
}