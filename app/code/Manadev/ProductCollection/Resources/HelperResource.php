<?php
/**
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */

namespace Manadev\ProductCollection\Resources;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Manadev\ProductCollection\Contracts\Filter;

class HelperResource extends Db\AbstractDb
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var TaxResource
     */
    protected $taxResource;
    protected $priceExpr;
    protected $currencyRate;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(Db\Context $context, PriceCurrencyInterface $priceCurrency,
        TaxResource $taxResource, StoreManagerInterface $storeManager, $resourcePrefix = null)
    {
        parent::__construct($context, $resourcePrefix);
        $this->priceCurrency = $priceCurrency;
        $this->taxResource = $taxResource;
        $this->storeManager = $storeManager;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct() {
        $this->_setMainTable('catalog_product_entity');
    }

    public function getPriceExpression() {
        if (!$this->priceExpr) {
            $this->priceExpr = "`price_index`.`min_price`";
            $this->priceExpr = $this->taxResource->applyTaxToPriceExpression($this->priceExpr);
            $this->priceExpr = $this->applyCurrencyRateToPriceExpression($this->priceExpr);
            $this->priceExpr = "ROUND($this->priceExpr, 2)";
        }

        return $this->priceExpr;
    }

    public function formatPriceRangeFacet(&$item, $from, $to, $isFirst, $isLast) {
        if ($isFirst) {
            $item['label'] = __("Below %1", $this->priceCurrency->format($to));
            if (!isset($item['value'])) {
                $item['value'] = "-{$to}";
            }
        }
        elseif ($isLast) {
            $item['label'] = __("%1 and above", $this->priceCurrency->format($from));
            if (!isset($item['value'])) {
                $item['value'] = "{$from}-";
            }
        }
        else {
            $item['label'] = __("%1 - %2", $this->priceCurrency->format($from), $this->priceCurrency->format($to));
            if (!isset($item['value'])) {
                $item['value'] = "{$from}-{$to}";
            }
        }
    }

    public function formatDecimal($value) {
        return sprintf("%d", round($value));
    }

    public function formatDecimalRangeFacet(&$item, $from, $to, $isFirst, $isLast) {
        if ($isFirst) {
            $item['label'] = __("Below %1", $this->formatDecimal($to));
            if (!isset($item['value'])) {
                $item['value'] = "-{$to}";
            }
        }
        elseif ($isLast) {
            $item['label'] = __("%1 and above", $this->formatDecimal($from));
            if (!isset($item['value'])) {
                $item['value'] = "{$from}-";
            }
        }
        else {
            $item['label'] = __("%1 - %2", $this->formatDecimal($from), $this->formatDecimal($to));
            if (!isset($item['value'])) {
                $item['value'] = "{$from}-{$to}";
            }
        }
    }

    public function formatCustomRangeFacet(&$item, $from, $to, $format, $showThousandSeparator) {
        if($showThousandSeparator) {
            $from = number_format($from);
            $to = number_format($to);
        }
        $item['label'] = __("%1 - %2", str_replace("0", $from, $format), str_replace("0", $to, $format));
        if (!isset($item['value'])) {
            $item['value'] = "{$from}-{$to}";
        }
    }

    public function formatDropdownRangeFacet(&$item, $from, $to) {
        $item['label'] = __("%1 - %2", $from, $to);
        if (!isset($item['value'])) {
            $item['value'] = "{$from}-{$to}";
        }
    }

    public function clearFacetSelect(Select $select) {
        $select->reset(Select::COLUMNS);
        $select->reset(Select::ORDER);
        $select->reset(Select::GROUP);
        $select->reset(Select::LIMIT_COUNT);
        $select->reset(Select::LIMIT_OFFSET);
    }

    public function addAppliedRanges(&$counts, $range, $appliedRanges) {
        foreach ($appliedRanges as $appliedRange) {
            list($from, $to) = $appliedRange;
            $index = $from === '' ? floor($to / $range) - 1 : floor($from / $range);

            $found = false;

            foreach ($counts as &$item) {
                if ($item['range'] == $index) {
                    $found = true;
                    $item['is_selected'] = true;
                    $item['value'] = "{$from}-{$to}";
                    break;
                }
            }

            if (!$found) {
                $counts[] = ['range' => $index, 'count' => 0, 'is_selected' => true];
            }
        }
        usort($counts, function($a, $b) {
            if ((int)$a['range'] < (int)$b['range']) return -1;
            if ((int)$a['range'] > (int)$b['range']) return 1;
            return 0;
        });
    }

    protected function applyCurrencyRateToPriceExpression($priceExpr) {
        if ($this->getCurrencyRate() == 1) {
            return $priceExpr;
        }

        return "($priceExpr)*" . round($this->getCurrencyRate(), 4);
    }

    public function getCurrencyRate()
    {
        if ($this->currencyRate === null) {
            $this->currencyRate = $this->storeManager->getStore()->getCurrentCurrencyRate();
            if (!$this->currencyRate) {
                $this->currencyRate = 1;
            }
        }

        return $this->currencyRate;
    }

    public function dontApplyFilterNamed($name) {
        return function (Filter $filter) use ($name) {
            return $filter->getFullName() != 'layered_nav_' . $name;
        };
    }

    public function dontApplyLayeredNavigationFilters() {
        return function (Filter $filter) {
            return strpos($filter->getFullName(), 'layered_nav_') !== 0;
        };
    }

    public function getEavExpr(Select $select, $tableName, $attributeId){
        $storeId = $this->storeManager->getStore()->getId();
        $db = $this->getConnection();

        $from = $select->getPart(Select::FROM);

        if (!isset($from['eav'])) {
            $select->joinInner(array('eav' => $tableName),
                "`eav`.`entity_id` = `e`.`entity_id` AND
                {$db->quoteInto("`eav`.`attribute_id` = ?", $attributeId)} AND
                {$db->quoteInto("`eav`.`store_id` = ?", $storeId)}", null);
        }

        return "`eav`.`value`";
    }

}