<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\ProductCollection\Contracts;

use Manadev\ProductCollection\Query;

abstract class Facet
{
    protected $name;
    protected $data = false;

    /**
     * @var Query
     */
    protected $query;

    public function __construct($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    public function addRecord($record) {
        if (!$this->data) {
            $this->data = [];
        }
        $this->data[] = $record;
    }

    /**
     * @return Query
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * @param Query $query
     */
    public function setQuery($query) {
        $this->query = $query;
    }


    abstract public function getType();
}