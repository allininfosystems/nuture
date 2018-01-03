<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\ProductCollection\Resources\Filters;

use Magento\Framework\DB\Select;
use Manadev\ProductCollection\Contracts\Filter;
use Manadev\ProductCollection\Contracts\FilterResource;
use Manadev\ProductCollection\Enums\Operation;
use Manadev\ProductCollection\Filters\LogicalFilter;

class LogicalFilterResource extends FilterResource
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_setMainTable('catalog_product_entity');
    }

    /**
     * @param Select $select
     * @param Filter $filter
     * @param $callback
     * @return false|string
     */
    public function apply(Select $select, Filter $filter, $callback) {
        /* @var $filter LogicalFilter */
        $operands = $filter->getOperands();
        if (!count($operands)) {
            return false;
        }

        $queryEngine = $this->factory->getMysqlQueryEngine();

        if ($filter->getOperator() == Operation::LOGICAL_NOT) {
            if ($condition = $queryEngine->applyFilterToSelectRecursively($select, array_values($operands)[0], $callback)) {
                return "NOT $condition";
            }
        }
        else {
            $operator = strtoupper($filter->getOperator());
            $combinedCondition = '';
            foreach ($operands as $operand) {
                if ($condition = $queryEngine->applyFilterToSelectRecursively($select, $operand, $callback)) {
                    if ($combinedCondition) {
                        $combinedCondition .= ' ' . $operator . ' ';
                    }
                    $combinedCondition .= '(' . $condition . ')';
                }
            }
            if ($combinedCondition !== '') {
                return $combinedCondition;
            }
        }

        return false;
    }
}