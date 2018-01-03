<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\ProductCollection\Resources\Facets\Dropdown;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db;
use Magento\Store\Model\StoreManagerInterface;
use Manadev\Core\Helpers\DbHelper;
use Manadev\ProductCollection\Configuration;
use Manadev\ProductCollection\Contracts\Facet;
use Manadev\ProductCollection\Contracts\FacetBatch;
use Manadev\ProductCollection\Facets\Dropdown\OptimizedFacet;
use Manadev\ProductCollection\FacetSorter;
use Manadev\ProductCollection\Factory;
use Manadev\ProductCollection\Resources\HelperResource;

class OptimizedFacetBatchResource extends OptimizedFacetResource {
    /**
     * @var DbHelper
     */
    protected $dbHelper;

    public function __construct(Db\Context $context, Factory $factory, StoreManagerInterface $storeManager,
        Configuration $configuration, HelperResource $helperResource, DbHelper $dbHelper, FacetSorter $sorter, $resourcePrefix = null)
    {
        parent::__construct($context, $factory, $storeManager, $configuration, $helperResource, $sorter, $resourcePrefix);
        $this->dbHelper = $dbHelper;
    }

    protected function joinEavIndex(Select $select, $facet) {
        /* @var FacetBatch $facet */
        $db = $this->getConnection();


        $select
            ->joinInner(array('eav' => $this->getTable('catalog_product_index_eav')),
                "`eav`.`entity_id` = `e`.`entity_id` AND
                {$db->quoteInto("`eav`.`attribute_id` IN (?)", $facet->getAttributeIds())} AND
                {$db->quoteInto("`eav`.`store_id` = ?", $this->getStoreId())}",
                array('count' => "COUNT(DISTINCT `eav`.`entity_id`)")
            );
    }

    public function getFields(Facet $facet) {
        return array_merge([
            'attribute_id' => new \Zend_Db_Expr("`eav`.`attribute_id`"),
        ], parent::getFields($facet));
    }

    public function count(Select $select, Facet $facet) {
        /* @var FacetBatch $facet */
        $this->prepareSelect($select, $facet);
        foreach ($this->dbHelper->fetchAllPaged($this->getConnection(), $select) as $record) {
            $facet->getFacet($record)->addRecord($record);
        }
        foreach ($facet->getFacets() as $individualFacet) {
            /* @var OptimizedFacet $individualFacet */
            $minimumOptionCount = $individualFacet->getHideWithSingleVisibleItem() ? 2 : 1;
            if (!($data = $individualFacet->getData())) {
                continue;
            }

            if (count($individualFacet->getData()) >= $minimumOptionCount) {
                continue;
            }

            $individualFacet->setData(false);
        }
    }
}