<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\ProductCollection\Resources\Facets\Dropdown;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db;
use Magento\Store\Model\StoreManagerInterface;
use Manadev\ProductCollection\Configuration;
use Manadev\ProductCollection\Contracts\Facet;
use Manadev\ProductCollection\Contracts\FacetResource;
use Manadev\ProductCollection\Facets\Dropdown\OptimizedFacet;
use Manadev\ProductCollection\FacetSorter;
use Manadev\ProductCollection\Factory;
use Manadev\ProductCollection\Resources\HelperResource;
use Zend_Db_Expr;

class OptimizedFacetResource extends FacetResource
{
    /**
     * @var FacetSorter
     */
    protected $sorter;

    public function __construct(Db\Context $context, Factory $factory,
        StoreManagerInterface $storeManager, Configuration $configuration,
        HelperResource $helperResource, FacetSorter $sorter, $resourcePrefix = null)
    {
        parent::__construct($context, $factory, $storeManager, $configuration,
            $helperResource, $resourcePrefix);
        $this->sorter = $sorter;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_setMainTable('catalog_product_index_eav');
    }

    /**
     * @param Select $select
     * @param Facet $facet
     * @return mixed
     */
    public function count(Select $select, Facet $facet) {
        /* @var $facet OptimizedFacet */
        $this->prepareSelect($select, $facet);
        $result = $this->getConnection()->fetchAll($select);
        $minimumOptionCount = $facet->getHideWithSingleVisibleItem() ? 2 : 1;
        return count($result) >= $minimumOptionCount ? $result : false;
    }

    public function prepareSelect(Select $select, Facet $facet) {
        /* @var $facet OptimizedFacet */
        $this->helperResource->clearFacetSelect($select);

        $fields = $this->getFields($facet);
        $this->addJoins($select, $facet);
        $select->columns($fields)->group($fields);

        return $select;
    }

    public function getFilterCallback(Facet $facet) {
        return $this->helperResource->dontApplyFilterNamed($facet->getName());
    }

    public function getFields(Facet $facet) {
        /* @var $facet OptimizedFacet */
        $selectedOptionIds = $facet->getSelectedOptionIds();
        $isSelectedExpr = $selectedOptionIds !== false
            ? "`eav`.`value` IN (" . implode(',', $selectedOptionIds). ")"
            : "1 <> 1";

        return [
            'sort_order' => new Zend_Db_Expr("`o`.`sort_order`"),
            'value' => new Zend_Db_Expr("`eav`.`value`"),
            'label' => new Zend_Db_Expr("COALESCE(`vs`.`value`, `vg`.`value`)"),
            'is_selected' => new Zend_Db_Expr($isSelectedExpr),
        ];
    }

    public function addJoins(Select $select, Facet $facet) {
        $this->joinEavIndex($select, $facet);
        $this->joinOptions($select);
    }

    /**
     * @param Facet $facet
     * @return string|null
     */
    public function getBatchType($facet) {
        /* @var $facet OptimizedFacet */
        if ($facet->getSelectedOptionIds()) {
            return null;
        }

        return 'dropdown_optimized_batch';
    }

    protected function joinOptions(Select $select) {
        $db = $this->getConnection();
        $select
            ->joinInner(array('o' => $this->getTable('eav_attribute_option')),
                "`o`.`option_id` = `eav`.`value`", null)
            ->joinInner(array('vg' => $this->getTable('eav_attribute_option_value')),
                $db->quoteInto("`vg`.`option_id` = `eav`.`value` AND `vg`.`store_id` = ?", 0), null)
            ->joinLeft(array('vs' => $this->getTable('eav_attribute_option_value')),
                $db->quoteInto("`vs`.`option_id` = `eav`.`value` AND `vs`.`store_id` = ?", $this->getStoreId()), null);
    }

    protected function joinEavIndex(Select $select, $facet) {
        /* @var OptimizedFacet $facet */
        $db = $this->getConnection();

        $select
            ->joinInner(array('eav' => $this->getTable('catalog_product_index_eav')),
                "`eav`.`entity_id` = `e`.`entity_id` AND
                {$db->quoteInto("`eav`.`attribute_id` = ?", $facet->getAttributeId())} AND
                {$db->quoteInto("`eav`.`store_id` = ?", $this->getStoreId())}",
                array('count' => "COUNT(DISTINCT `eav`.`entity_id`)")
            );
    }

    public function sort(Facet $facet) {
        $this->sorter->sort($facet);
    }
}