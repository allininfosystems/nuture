<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPoints Calculator Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Helper;
class Calculator extends \Magento\Framework\App\Helper\AbstractHelper {
    protected $helper;
    protected $_taxConfig;
    protected $_taxCalculation;

    const XML_PATH_ROUNDING_METHOD = 'rewardpoints/earning/rounding_method';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Rewardpoints\Helper\Config $globalConfig,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\Config $taxConfig
    )
    {
        $this->helper = $globalConfig;
        $this->_taxConfig = $taxConfig;
        $this->_taxCalculation = $taxCalculation;
        parent::__construct($context);
    }

    /**
     * Rounding number by reward points configuration
     * 
     * @param mixed $number
     * @param mixed $store
     * @return int
     */
    public function round($number, $store = null) {
        switch ($this->helper->getConfig(self::XML_PATH_ROUNDING_METHOD, $store)) {
            case 'floor':
                return floor($number);
            case 'ceil':
                return ceil($number);
        }
        return round($number);
    }

    /**
     * Calculate price including tax or excluding tax
     * 
     * @param type $product
     * @param type $price
     * @param type $includingTax return type (includingTax of excludingTax)
     * @param type $item
     * @return type price
     */
    public function getPrice($product, $price, $includingTax = null, $item = false) {
        if (!$price) {
            return $price;
        }
        $store = Mage::app()->getStore();

        if ($item)
            $priceIncludingTax = false;
        else
            $priceIncludingTax = $this->_taxConfig->priceIncludesTax($store);

        if (($priceIncludingTax && $includingTax) || (!$priceIncludingTax && !$includingTax)) {
            return $price;
        }

        $percent = $product->getTaxPercent();
        $includingPercent = null;

        $taxClassId = $product->getTaxClassId();
        if (is_null($percent)) {
            if ($taxClassId) {
                $request = $this->_taxCalculation
                        ->getRateRequest(null, null, null, $store);
                $percent = $this->_taxCalculation
                        ->getRate($request->setProductClassId($taxClassId));
            }
        }
        if ($taxClassId && $priceIncludingTax) {
            $request = $this->_taxCalculation->getRateRequest(false, false, false, $store);
            $includingPercent = $this->_taxCalculation
                    ->getRate($request->setProductClassId($taxClassId));
        }
        if ($percent === false || is_null($percent) || $percent == 0) {
            if ($priceIncludingTax && !$includingPercent) {
                return $price;
            }
        }
        $product->setTaxPercent($percent);
        if ($includingTax && !$priceIncludingTax) {
            $price = $this->_calculatePrice($price, $percent, true);
        } else {
            if ($includingPercent != $percent) {
                $price = $this->_calculatePrice($price, $includingPercent, false);
                if ($percent != 0) {
                    $price = $this->_taxCalculation->round($price);
                    $price = $this->_calculatePrice($price, $percent, true);
                }
            } else
                $price = $this->_calculatePrice($price, $percent, false);
        }
        return $store->roundPrice($price);
    }

    protected function _calculatePrice($price, $percent, $type) {
        $calculator = $this->_taxCalculation;
        if ($type) {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, false, false);
            return $price + $taxAmount;
        } else {
            $taxAmount = $calculator->calcTaxAmount($price, $percent, true, false);
            return $price - $taxAmount;
        }
    }

}
