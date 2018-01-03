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
class PointAfterTax extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkOutSession;
    
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magestore\Rewardpoints\Helper\Config
     */
    protected $_helper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
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
     * @return $this|bool
     */
    public function checkOutput($quote,$address,$session){
        $applyTaxAfterDiscount = (bool) $this->_helper->getConfig(
            \Magento\Tax\Model\Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT, $quote->getStoreId()
        );
        if ($applyTaxAfterDiscount) {
            $this->_processHiddenTaxes($address);
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
            return $this;
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
                $rule = $helper->getQuoteRule($ruleData['rule_id']);
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
                $this->_prepareDiscountForTaxAmount($shippingAssignment, $ruleDiscount, $points, $rule);
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
                        if ($rule->getId() == 'rate') {
                            $this->_prepareDiscountForTaxAmount($shippingAssignment, $ruleDiscount, $points);
                        } else {
                            $this->_prepareDiscountForTaxAmount($shippingAssignment, $ruleDiscount, $points, $rule);
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
            $this->setDiscount($baseDiscount,$total,$address,$pointUsed,$quote);
        }
        return $this;
    }

    /**
     * @param $baseDiscount
     * @param $total
     * @param $address
     * @param $pointUsed
     * @param $quote
     */
    public function setDiscount($baseDiscount,$total,$address,$pointUsed,$quote){
        $discount =  $this->_priceCurrency->convert($baseDiscount);
        $total->addTotalAmount('rewardpoints', -$discount);
        $total->addBaseTotalAmount('rewardpoints', -$baseDiscount);
        $total->setBaseGrandTotal($address->getBaseGrandTotal() - $baseDiscount);
        $total->setGrandTotal($address->getGrandTotal() - $discount);
        $total->setRewardpointsSpent($address->getRewardpointsSpent() + $pointUsed);
        $total->setRewardpointsBaseDiscount($address->getRewardpointsBaseDiscount() + $baseDiscount);
        $total->setRewardpointsDiscount($address->getRewardpointsDiscount() + $discount);
        $quote->setRewardpointsSpent($total->getRewardpointsSpent());
        $quote->setRewardpointsBaseDiscount($total->getRewardpointsBaseDiscount());
        $quote->setRewardpointsDiscount($total->getRewardpointsDiscount());
        $address->setMagestoreBaseDiscount($address->getMagestoreBaseDiscount() + $baseDiscount);
        $quote->setMagestoreBaseDiscount($quote->getRewardpointsBaseDiscount() + $baseDiscount);
    }

    /**
     * Prepare Discount Amount used for Tax
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param type $baseDiscount
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function _prepareDiscountForTaxAmount(\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment, $baseDiscount, $points, $rule = null) {
        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $address->getAllItems();
        if (!count($items))
            return $this;
        // Calculate total item prices
        $baseItemsPrice = 0;
        $spendHelper = $this->_calculationSpending;
        $baseParentItemsPrice = array();
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $baseParentItemsPrice[$item->getId()] = 0;
                foreach ($item->getChildren() as $child) {
                    if ($rule !== null && !$rule->getActions()->validate($child))
                        continue;
                    $baseParentItemsPrice[$item->getId()] = $item->getQty() * ($child->getQty() * $spendHelper->_getItemBasePrice($child)) - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                }
                $baseItemsPrice += $baseParentItemsPrice[$item->getId()];
            } elseif ($item->getProduct()) {
                if ($rule !== null && !$rule->getActions()->validate($item))
                    continue;
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
                foreach ($item->getChildren() as $child) {
                    if ($parentItemBaseDiscount <= 0)
                        break;
                    if ($rule !== null && !$rule->getActions()->validate($child))
                        continue;
                    $baseItemPrice = $item->getQty() * ($child->getQty() * $spendHelper->_getItemBasePrice($child)) - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                    $itemBaseDiscount = min($baseItemPrice, $parentItemBaseDiscount);
                    $parentItemBaseDiscount -= $itemBaseDiscount;
                    $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
                    $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                    $child->setRewardpointsBaseDiscount($child->getRewardpointsBaseDiscount() + $itemBaseDiscount)
                        ->setRewardpointsDiscount($child->getRewardpointsDiscount() + $itemDiscount)
                        ->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemBaseDiscount)
                        ->setRewardpointsSpent($child->getRewardpointsSpent() + $pointSpent);
                }
            } elseif ($item->getProduct()) {
                if ($rule !== null && !$rule->getActions()->validate($item))
                    continue;
                $baseItemPrice = $item->getQty() * $spendHelper->_getItemBasePrice($item) - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                $itemBaseDiscount = $baseDiscount * $baseItemPrice / $baseItemsPrice;
                $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
                $pointSpent = round($points * $baseItemPrice / $baseItemsPrice, 0, PHP_ROUND_HALF_DOWN);
                $item->setRewardpointsBaseDiscount($item->getRewardpointsBaseDiscount() + $itemBaseDiscount)
                    ->setRewardpointsDiscount($item->getRewardpointsDiscount() + $itemDiscount)
                    ->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemBaseDiscount)
                    ->setRewardpointsSpent($item->getRewardpointsSpent() + $pointSpent);
            }
        }
        if ($baseDiscountForShipping) {
            $this->baseDiscountForShipping($address,$baseDiscountForShipping);
        }
        return $this;
    }

    /**
     * @param $address
     * @param $baseDiscountForShipping
     */
    public function baseDiscountForShipping($address,$baseDiscountForShipping){
        $shippingAmount = $address->getShippingAmountForDiscount();
        if ($shippingAmount !== null) {
            $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
        } else {
            $baseShippingAmount = $address->getBaseShippingAmount();
        }
        $baseShipping = $baseShippingAmount - $address->getBaseShippingDiscountAmount() - $address->getMagestoreBaseDiscountForShipping();
        $itemBaseDiscount = ($baseDiscountForShipping <= $baseShipping) ? $baseDiscountForShipping : $baseShipping; //$baseDiscount * $address->getBaseShippingAmount() / $baseItemsPrice;
        $itemDiscount = $this->_priceCurrency->convert($itemBaseDiscount);
        $address->setRewardpointsBaseAmount($address->getRewardpointsBaseAmount() + $itemBaseDiscount)
            ->setRewardpointsAmount($address->getRewardpointsAmount() + $itemDiscount)
            ->setMagestoreBaseDiscountForShipping($address->getMagestoreBaseDiscountForShipping() + $itemBaseDiscount);
    }

    protected function _processHiddenTaxes($address) {
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setHiddenTaxAmount($child->getHiddenTaxAmount() + $child->getRewardpointsHiddenTaxAmount());
                    $child->setBaseHiddenTaxAmount($child->getBaseHiddenTaxAmount() + $child->getRewardpointsBaseHiddenTaxAmount());

                    $address->addTotalAmount('hidden_tax', $child->getRewardpointsHiddenTaxAmount());
                    $address->addBaseTotalAmount('hidden_tax', $child->getRewardpointsBaseHiddenTaxAmount());
                }
            } elseif ($item->getProduct()) {
                $item->setHiddenTaxAmount($item->getHiddenTaxAmount() + $item->getRewardpointsHiddenTaxAmount());
                $item->setBaseHiddenTaxAmount($item->getBaseHiddenTaxAmount() + $item->getRewardpointsBaseHiddenTaxAmount());

                $address->addTotalAmount('hidden_tax', $item->getRewardpointsHiddenTaxAmount());
                $address->addBaseTotalAmount('hidden_tax', $item->getRewardpointsBaseHiddenTaxAmount());
            }
        }
        if ($address->getRewardpointsShippingHiddenTaxAmount()) {
            $address->addTotalAmount('shipping_hidden_tax', $address->getRewardpointsShippingHiddenTaxAmount());
            $address->addBaseTotalAmount('shipping_hidden_tax', $address->getRewardpointsBaseShippingHiddenTaxAmount());
        }
    }

}
