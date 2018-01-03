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
 * Rewardpoints Spend for Order by Point Model
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Model\Total\Quote;
class Point extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var int
     */
    protected $_hiddentBaseDiscount = 0;
    /**
     * @var int
     */
    protected $_hiddentDiscount = 0;

    /**
     * @var \Magestore\Rewardpoints\Helper\Config
     */
    protected $_helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkOutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    /**
     * @var \Magestore\Rewardpoints\Helper\Data
     */
    protected $_helperData;
    /**
     * @var \Magestore\Rewardpoints\Helper\Block\Spend
     */
    protected $_blockSpend;
    /**
     * @var \Magestore\Rewardpoints\Helper\Calculation\Spending
     */
    protected $_calculationSpending;
    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $_helperCustomer;
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelperData;
    /**
     * @var \Magento\Tax\Model\Calculation
     */
    protected $_taxModelCalculation;
    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxModelConfig;
    /**
     * Point constructor.
     * @param \Magestore\Rewardpoints\Helper\Config $globalConfig
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Rewardpoints\Helper\Data $helperData
     * @param \Magestore\Rewardpoints\Helper\Block\Spend $blockSpend
     * @param \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending
     * @param \Magestore\Rewardpoints\Helper\Customer $helperCustomer
     * @param \Magento\Tax\Helper\Data $taxHelperData
     * @param \Magento\Tax\Model\Calculation $taxModelCalculation
     * @param \Magento\Tax\Model\Config $taxModelConfig
     */
    public function __construct(
        \Magestore\Rewardpoints\Helper\Config $globalConfig,
        \Magento\Checkout\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Rewardpoints\Helper\Data $helperData,
        \Magestore\Rewardpoints\Helper\Block\Spend $blockSpend,
        \Magestore\Rewardpoints\Helper\Calculation\Spending $calculationSpending,
        \Magestore\Rewardpoints\Helper\Customer $helperCustomer,
        \Magento\Tax\Helper\Data $taxHelperData,
        \Magento\Tax\Model\Calculation $taxModelCalculation,
        \Magento\Tax\Model\Config $taxModelConfig
    ) {

        $this->setCode('rewardpoint');
        $this->_helper = $globalConfig;
        $this->_checkOutSession = $session;
        $this->_storeManager = $storeManager;
        $this->_priceCurrency = $priceCurrency;
        $this->_helperData = $helperData;
        $this->_blockSpend = $blockSpend;
        $this->_calculationSpending = $calculationSpending;
        $this->_helperCustomer = $helperCustomer;
        $this->_taxHelperData = $taxHelperData;
        $this->_taxModelCalculation = $taxModelCalculation;
        $this->_taxModelConfig = $taxModelConfig;
    }

    /**
     * @param $quote
     * @param $address
     * @param $session
     * @return bool
     */
    public function checkOutput($quote,$address,$session){
        $applyTaxAfterDiscount = (bool) $this->_helper->getConfig(
            \Magento\Tax\Model\Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $quote->getStoreId()
        );
        if (!$applyTaxAfterDiscount) {
            return true;
        }
        if (!$this->_helperData->isEnable($quote->getStoreId())) {
            return true;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return true;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return true;
        }
        if (!$session->getData('use_point')) {
            return true;
        }
        return false;
    }

    /**
     * collect reward points total
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {

        parent::collect($quote, $shippingAssignment, $total);
            $address = $shippingAssignment->getShipping()->getAddress();
            $session = $this->_checkOutSession;
            if($this->checkOutput($quote,$address,$session)){
                return $this;
            }
            $rewardSalesRules = $session->getRewardSalesRules();
            $rewardCheckedRules = $session->getRewardCheckedRules();

            if (!$rewardSalesRules && !$rewardCheckedRules) {
                return $this;
            }
            /** @var $helper Magestore_RewardPoints_Helper_Calculation_Spending */
            $helper = $this->_calculationSpending;
            $baseTotal = $helper->getQuoteBaseTotal($quote, $address);
            $maxPoints = $this->_helperCustomer->getBalance();
            if ($maxPointsPerOrder = $helper->getMaxPointsPerOrder($quote->getStoreId())) {
                $maxPoints = min($maxPointsPerOrder, $maxPoints);
            }
            $maxPoints -= $helper->getPointItemSpent();
            if ($maxPoints <= 0) {
                $session->setRewardCheckedRules(array());
                $session->setRewardSalesRules(array());
                return $this;
            }
            $baseDiscount = 0;
            $pointUsed = 0;
            // Checked Rules Discount First
            if (is_array($rewardCheckedRules)) {
                $newRewardCheckedRules = array();
                foreach ($rewardCheckedRules as $ruleData) {
                    if ($baseTotal < 0.0001)
                        break;
                    $rule = $helper->getQuoteRule();
                    if (!$rule || !$rule->getId() || $rule->getSimpleAction() != 'fixed') {
                        continue;
                    }
                    if ($maxPoints < $rule->getPointsSpended()) {
                        $session->addNotice(__('You cannot spend more than %s points per order', $helper->getMaxPointsPerOrder($quote->getStoreId())));
                        continue;
                    }
                    $points = $rule->getPointsSpended();
                    $ruleDiscount = $helper->getQuoteRuleDiscount($quote, $rule, $points);
                    if ($ruleDiscount < 0.0001) {
                        continue;
                    }
                    $baseTotal -= $ruleDiscount;
                    $maxPoints -= $points;
                    $baseDiscount += $ruleDiscount;
                    $pointUsed += $points;

                    $newRewardCheckedRules[$rule->getId()] = array(
                        'rule_id' => $rule->getId(),
                        'use_point' => $points,
                        'base_discount' => $ruleDiscount,
                    );
                    $this->_prepareDiscountForTaxAmount($total,$address, $ruleDiscount, $points, $rule);
                    if ($rule->getStopRulesProcessing()) {
                        break;
                    }
                }
                $session->setRewardCheckedRules($newRewardCheckedRules);
            }
            // Sales Rule (slider) discount Last
            if (is_array($rewardSalesRules)) {
                $newRewardSalesRules = array();
                if ($baseTotal > 0.0 && isset($rewardSalesRules['rule_id'])) {
                    $rule = $helper->getQuoteRule($rewardSalesRules['rule_id']);
                    if ($rule && $rule->getId() && $rule->getSimpleAction() == 'by_price') {
                        $points = min($rewardSalesRules['use_point'], $maxPoints);
                        $ruleDiscount = $helper->getQuoteRuleDiscount($quote, $rule, $points);
                        if ($ruleDiscount > 0.0) {
                            $baseTotal -= $ruleDiscount;
                            $maxPoints -= $points;
                            $baseDiscount += $ruleDiscount;
                            $pointUsed += $points;
                            $newRewardSalesRules = array(
                                'rule_id' => $rule->getId(),
                                'use_point' => $points,
                                'base_discount' => $ruleDiscount,
                            );
                            if($rule->getId() == 'rate'){
                                $this->_prepareDiscountForTaxAmount($total, $address, $ruleDiscount, $points);
                            } else {
                                $this->_prepareDiscountForTaxAmount($total, $address, $ruleDiscount, $points, $rule);
                            }
                        }
                    }
                }
                $session->setRewardSalesRules($newRewardSalesRules);
            }
            // verify quote total data
            if ($baseTotal < 0.0001) {
                $baseTotal = 0.0;
                $baseDiscount = $helper->getQuoteBaseTotal($quote, $address);
            }
            if ($baseDiscount) {
                $this->setBaseDiscount($baseDiscount,$total,$quote,$pointUsed);
            }
        return $this;
    }

    /**
     * @param $baseDiscount
     * @param $total
     * @param $quote
     * @param $pointUsed
     */
    public function setBaseDiscount($baseDiscount,$total,$quote,$pointUsed){
        $discount =  $this->_priceCurrency->convert($baseDiscount);
        $total->addTotalAmount('rewardpoints', -$discount);
        $total->addBaseTotalAmount('rewardpoints', -$baseDiscount);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseDiscount);
        $total->setGrandTotal($total->getGrandTotal() - $discount);
        $total->setRewardpointsSpent($total->getRewardpointsSpent() + $pointUsed);
        $total->setRewardpointsBaseDiscount($total->getRewardpointsBaseDiscount() + $baseDiscount);
        $total->setRewardpointsDiscount($total->getRewardpointsDiscount() + $discount);
        $quote->setRewardpointsSpent($total->getRewardpointsSpent());
        $quote->setRewardpointsBaseDiscount($total->getRewardpointsBaseDiscount());
        $quote->setRewardpointsDiscount($total->getRewardpointsDiscount());
        $quote->setMagestoreBaseDiscount($quote->getRewardpointsBaseDiscount() + $baseDiscount);
    }
    public function fetch(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $amount = $quote->getRewardpointsDiscount();
        if(strip_tags($amount)){
            return [
                [
                    'code' => 'use-point',
                    'title' => __('Use Point'),
                    'value' => -$amount,
                ],
            ];

        }
    }

    /**
     * Prepare Discount Amount used for Tax
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param type $baseDiscount
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function _prepareDiscountForTaxAmount(
        \Magento\Quote\Model\Quote\Address\Total $total,
        \Magento\Quote\Model\Quote\Address $address,
        $baseDiscount,
        $points,
        $rule = null)
    {
        $items = $address->getAllItems();
        if (!count($items))
            return $this;
        // Calculate total item prices
        $baseItemsPrice = 0;
        $store = $this->_storeManager->getStore();
        $spendHelper = $this->_calculationSpending;
        $baseParentItemsPrice = array();
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $baseParentItemsPrice[$item->getId()] = 0;
                foreach ($item->getChildren() as $child) {
                    if($rule!==null && !$rule->getActions()->validate($child)) continue;
                    $baseParentItemsPrice[$item->getId()] += $item->getQty() * ($child->getQty() * $spendHelper->_getItemBasePrice($child)) - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                }
                $baseItemsPrice += $baseParentItemsPrice[$item->getId()];
            } elseif ($item->getProduct()) {
                if($rule!==null && !$rule->getActions()->validate($item)) continue;
                $baseItemsPrice += $item->getQty() * $spendHelper->_getItemBasePrice($item) - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
            }
        }
        if ($baseItemsPrice < 0.0001)
            return $this;
        $discountForShipping = $this->_helper->getConfig(
                        \Magestore\Rewardpoints\Helper\Calculation\Spending::XML_PATH_SPEND_FOR_SHIPPING, $address->getQuote()->getStoreId()
        );
        if ($baseItemsPrice < $baseDiscount && $discountForShipping) {
            $baseDiscountForShipping = $baseDiscount - $baseItemsPrice;
            $baseDiscount = $baseItemsPrice;
        } else {
            $baseDiscountForShipping = 0;
        }
        // Update discount for each item
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $parentItemBaseDiscount = $baseDiscount * $baseParentItemsPrice[$item->getId()] / $baseItemsPrice;
                $this->hasChildren($item,$parentItemBaseDiscount,$rule,$spendHelper,$baseItemsPrice,$points,$address,$store);
            } elseif ($item->getProduct()) {
                if($rule!==null && !$rule->getActions()->validate($item)) continue;
                $this->notHasChildren($item,$spendHelper,$baseDiscount,$baseItemsPrice,$points);
            }
        }
        if ($baseDiscountForShipping > 0) {
            $this->baseDiscountForShipping($address,$baseDiscountForShipping,$total);
        }
        return $this;
    }

    /**
     * @param $item
     * @param $parentItemBaseDiscount
     * @param $rule
     * @param $spendHelper
     * @param $baseItemsPrice
     * @param $points
     * @param $address
     * @param $store
     */
    public function hasChildren($item,$parentItemBaseDiscount,$rule,$spendHelper,$baseItemsPrice,$points,$address,$store){
        foreach ($item->getChildren() as $child) {
            if ($parentItemBaseDiscount <= 0)
                break;
            if($rule!==null && !$rule->getActions()->validate($child)) continue;
            $baseItemPrice = $item->getQty() * ($child->getQty() * $spendHelper->_getItemBasePrice($child)) - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
            $itemBaseDiscount = min($baseItemPrice, $parentItemBaseDiscount); //$baseDiscount * $baseItemPrice / $baseItemsPrice;
            $parentItemBaseDiscount -= $itemBaseDiscount;
            $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
            $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
            $child->setRewardpointsBaseDiscount($child->getRewardpointsBaseDiscount() + $itemBaseDiscount)
                ->setRewardpointsDiscount($child->getRewardpointsDiscount() + $itemDiscount)
                ->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemBaseDiscount)
                ->setRewardpointsSpent($child->getRewardpointsSpent() + $pointSpent);
            $child->setDiscountAmount(max(0, $child->getDiscountAmount() + $itemDiscount));
            $child->setBaseDiscountAmount(max(0, $child->getBaseDiscountAmount() + $itemBaseDiscount));
            $baseTaxableAmount = $child->getBaseTaxableAmount();
            $taxableAmount = $child->getTaxableAmount();
            if ($this->_taxHelperData->priceIncludesTax()) {
                $rate = $this->getItemRateOnQuote($address, $child->getProduct(), $store);
                if ($rate > 0) {
                    $child->setRewardpointsBaseHiddenTaxAmount($this->calTax($baseTaxableAmount, $rate) - $this->calTax($item->getBaseTaxableAmount(), $rate) + $child->getRewardpointsBaseHiddenTaxAmount());
                    $child->setRewardpointsHiddenTaxAmount($this->calTax($taxableAmount, $rate) - $this->calTax($item->getTaxableAmount(), $rate) + $child->getRewardpointsHiddenTaxAmount());
                }
            }
        }
    }

    /**
     * @param $item
     * @param $spendHelper
     * @param $baseDiscount
     * @param $baseItemsPrice
     * @param $points
     * @param $address
     * @param $store
     * @param $baseTaxableAmount
     * @param $taxableAmount
     */
    public function notHasChildren($item,$spendHelper,$baseDiscount,$baseItemsPrice,$points){
        $baseItemPrice = $item->getQty() * $spendHelper->_getItemBasePrice($item) - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
        $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
        $itemDiscount =  $this->_priceCurrency->convert($itemBaseDiscount);
        $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
        $item->setRewardpointsBaseDiscount($item->getRewardpointsBaseDiscount() + $itemBaseDiscount)
            ->setRewardpointsDiscount($item->getRewardpointsDiscount() + $itemDiscount)
            ->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemBaseDiscount)
            ->setRewardpointsSpent($item->getRewardpointsSpent() + $pointSpent);
        $item->setDiscountAmount(max(0, $item->getDiscountAmount() + $itemDiscount));
        $item->setBaseDiscountAmount(max(0, $item->getBaseDiscountAmount() + $itemBaseDiscount));
    }
    /**
     * @param $address
     * @param $baseDiscountForShipping
     * @param $total
     * @param $store
     * @param $baseTaxableAmount
     * @param $taxableAmount
     */
    public function baseDiscountForShipping($address,$baseDiscountForShipping,$total){
        $shippingAmount = $address->getShippingAmountForDiscount();
        if ($shippingAmount !== null) {
            $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
        } else {
            $baseShippingAmount = $address->getBaseShippingAmount();
        }
        $baseShipping = $baseShippingAmount - $address->getBaseShippingDiscountAmount() - $address->getMagestoreBaseDiscountForShipping();
        $itemBaseDiscount = ($baseDiscountForShipping <= $baseShipping) ? $baseDiscountForShipping : $baseShipping;
        $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
        $address->getQuote()->setRewardpointsBaseAmount($address->getQuote()->getRewardpointsBaseAmount() + $itemBaseDiscount)
            ->setRewardpointsAmount($address->getQuote()->getRewardpointsAmount() + $itemDiscount)
            ->setMagestoreBaseDiscountForShipping($address->getQuote()->getMagestoreBaseDiscountForShipping() + $itemBaseDiscount);
        $total->setBaseShippingDiscountAmount(max(0, $total->getBaseShippingDiscountAmount() + $itemBaseDiscount));
        $total->setShippingDiscountAmount(max(0, $total->getShippingDiscountAmount() + $itemDiscount));

    }
    //get Rate
    public function getItemRateOnQuote($address, $product, $store) {
        $taxClassId = $product->getTaxClassId();
        if ($taxClassId) {
            $request = $this->_taxModelCalculation->getRateRequest(
                    $address, $address->getQuote()->getBillingAddress(), $address->getQuote()->getCustomerTaxClassId(), $store
            );
            $rate = $this->_taxModelCalculation->getRate($request->setProductClassId($taxClassId));
            return $rate;
        }
        return 0;
    }

    public function getShipingTaxRate($address, $store) {
        $request = $this->_taxModelCalculation->getRateRequest(
                $address, $address->getQuote()->getBillingAddress(), $address->getQuote()->getCustomerTaxClassId(), $store
        );
        $request->setProductClassId($this->_taxModelConfig->getShippingTaxClass($store));
        return $this->_taxModelCalculation->getRate($request);
    }

    public function calTax($price, $rate) {
        return $this->round($this->_taxModelCalculation->calcTaxAmount($price, $rate, true, false));
    }

    public function round($price) {
        return $this->_taxModelCalculation->round($price);
    }



}
