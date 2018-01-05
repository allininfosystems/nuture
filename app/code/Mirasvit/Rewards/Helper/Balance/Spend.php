<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Rewards\Helper\Balance;

use \Mirasvit\Rewards\Api\Config\Rule\SpendingStyleInterface;

class Spend extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Framework\Pricing\Helper\Data $currencyHelper,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Mirasvit\Rewards\Model\ResourceModel\Spending\Rule\CollectionFactory $spendingRuleCollectionFactory,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->taxConfig                     = $taxConfig;
        $this->currencyHelper                = $currencyHelper;
        $this->rewardsBalance                = $rewardsBalance;
        $this->spendingRuleCollectionFactory = $spendingRuleCollectionFactory;
        $this->config                        = $config;
        $this->context                       = $context;

        parent::__construct($context);
    }

    /**
     * @return \Mirasvit\Rewards\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function isRewardsIncludeTax()
    {
        return $this->getConfig()->getGeneralIsIncludeTaxSpending();
    }

    /**
     * If tax applied before discount
     *
     * @return bool
     */
    public function isIncludeTax()
    {
        return !$this->taxConfig->applyTaxAfterDiscount();
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return float
     */
    protected function getQuoteSubtotal($quote)
    {
        if ($this->isRewardsIncludeTax()) {
            $subtotal = $quote->getGrandTotal();
        } else {
            $subtotal = $quote->getSubtotalWithDiscount();
            if ($this->getConfig()->getGeneralIsSpendShipping() && !$quote->isVirtual()) {
                $subtotal += $this->getShippingAmount($quote);
            }
        }

        return $subtotal;
    }

    /**
     * @param \Magento\Quote\Model\Quote            $quote
     * @param \Mirasvit\Rewards\Model\Spending\Rule $rule
     *
     * @return float
     */
    protected function getLimitedSubtotal($quote, $rule)
    {
        $subtotal = 0;
        $priceIncludesTax = $this->isRewardsIncludeTax();
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getItemsCollection() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            if ($rule->getActions()->validate($item)) {
                if ($priceIncludesTax) {
                    $itemPrice = $item->getBasePrice();
                    if ($this->isRewardsIncludeTax()) {
                        $itemPrice = $item->getBasePriceInclTax();
                    }
                    $itemPrice += (float)$item->getWeeeTaxAppliedAmountInclTax();
                    $subtotal += $itemPrice * $item->getQty() - $item->getBaseDiscountAmount();
                } else {
                    $subtotal += $item->getBasePrice() * $item->getQty() - $item->getBaseDiscountAmount();
                }
            }
        }
        if ($this->getConfig()->getGeneralIsSpendShipping() && !$quote->isVirtual()) {
            $subtotal += $this->getShippingAmount($quote);
        }

        return $subtotal;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return float
     */
    private function getShippingAmount($quote)
    {
        $shippingAddress = $quote->getShippingAddress();

        if ($quote->getCartShippingMethod()) {
            $shippingAddress->setCollectShippingRates(true)->setShippingMethod(
                $quote->getCartShippingCarrier() . '_' . $quote->getCartShippingMethod()
            );
            $shippingAddress->collectShippingRates();
        }

        if ($this->isRewardsIncludeTax()) {
            return $shippingAddress->getShippingAmount() + $shippingAddress->getShippingTaxAmount();
        } else {
            return $shippingAddress->getShippingAmount();
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Mirasvit\Rewards\Model\ResourceModel\Spending\Rule\Collection
     */
    protected function getRules($quote)
    {
        $websiteId       = $quote->getStore()->getWebsiteId();
        $customerGroupId = $quote->getCustomerGroupId();

        return $this->getRuleCollection($websiteId, $customerGroupId);
    }

    /**
     * @param int $websiteId
     * @param int $customerGroupId
     * @return \Mirasvit\Rewards\Model\ResourceModel\Spending\Rule\Collection
     */
    private function getRuleCollection($websiteId, $customerGroupId)
    {
        $rules = $this->spendingRuleCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addCustomerGroupFilter($customerGroupId)
            ->addCurrentFilter()
        ;
        $rules->getSelect()->order('sort_order ASC');

        return $rules;
    }

    /**
     * @param int $points
     * @param int $websiteId
     * @param int $customerGroupId
     * @param int $subtotal
     *
     * @return string
     */
    public function getProductPointsAsMoney($points, $websiteId, $customerGroupId, $subtotal)
    {
        if ($this->config->getGeneralIsDisplayProductPointsAsMoney()) {
            $rules = $this->getRuleCollection($websiteId, $customerGroupId);
            if ($rules->count()) {
                $pointsMoney = [];
                foreach ($rules as $rule) {
                    $pointsMoney[] = ($points / $rule->getSpendPoints()) * $rule->getMonetaryStep($subtotal);
                }

                $points = $this->currencyHelper->currency(max($pointsMoney), true, false);
            }
        }

        return $points;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Framework\DataObject
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getCartRange($quote)
    {
        $rules = $this->getRules($quote);
        $balancePoints = $this->rewardsBalance->getBalancePoints($quote->getCustomerId());

        $minPoints = 0;
        $quoteSubTotal = $this->getQuoteSubtotal($quote);
        $totalPoints = 0;
        /** @var \Mirasvit\Rewards\Model\Spending\Rule $rule */
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if ($quote->getItemVirtualQty() > 0) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
            if ($quoteSubTotal > 0 && $rule->validate($address)) {
                $ruleSubTotal = $this->getLimitedSubtotal($quote, $rule);
                if ($ruleSubTotal > $quoteSubTotal) {
                    $ruleSubTotal = $quoteSubTotal;
                }

                $ruleMinPoints   = $rule->getSpendMinAmount($ruleSubTotal);
                $ruleMaxPoints   = $rule->getSpendMaxAmount($ruleSubTotal);
                $ruleSpendPoints = $rule->getSpendPoints();
                $monetaryStep    = $rule->getMonetaryStep($ruleSubTotal);

                if (($ruleMinPoints && ($quoteSubTotal / $monetaryStep) < 1) || $ruleMinPoints > $ruleMaxPoints) {
                    continue;
                }

                $ruleMinPoints = $ruleMinPoints ? max($ruleMinPoints, $ruleSpendPoints) : $ruleSpendPoints;

                $minPoints = $minPoints ? min($minPoints, $ruleMinPoints) : $ruleMinPoints;

                if ($ruleMinPoints <= $ruleMaxPoints) {
                    $quoteSubTotal -= $ruleMaxPoints / $ruleSpendPoints * $monetaryStep;
                    $totalPoints   += $ruleMaxPoints;
                }

                if (
                    $rule->getSpendingStyle() == SpendingStyleInterface::STYLE_FULL &&
                    $totalPoints > $balancePoints
                ) {
                    $totalPoints = floor($balancePoints / $ruleSpendPoints) * $ruleSpendPoints;
                }

                if ($rule->getIsStopProcessing()) {
                    break;
                }
            }
        }
        if ($minPoints > $totalPoints) {
            $minPoints = $totalPoints = 0;
        }

        return new \Magento\Framework\DataObject(['min_points' => $minPoints, 'max_points' => $totalPoints]);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $pointsNumber
     * @return \Magento\Framework\DataObject
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getCartPoints($quote, $pointsNumber)
    {
        $rules = $this->getRules($quote);
        $totalAmount = 0;
        $totalPoints = 0;
        /** @var \Mirasvit\Rewards\Model\Spending\Rule $rule */
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if ($quote->getItemVirtualQty() > 0) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
            if ($pointsNumber > 0 && $rule->validate($address)) {
                $subtotal         = $this->getLimitedSubtotal($quote, $rule);
                $ruleMaxPoints    = $rule->getSpendMaxAmount($subtotal);
                $rulePointsNumber = $pointsNumber;
                if ($ruleMaxPoints && $pointsNumber > $ruleMaxPoints) {
                    $rulePointsNumber = $ruleMaxPoints;
                }

                if ($rule->getSpendingStyle() == SpendingStyleInterface::STYLE_PARTIAL) {
                    $stepsSecond = round($rulePointsNumber / $rule->getSpendPoints(), 2, PHP_ROUND_HALF_DOWN);
                } else {
                    $rulePointsNumber = floor($rulePointsNumber / $rule->getSpendPoints()) * $rule->getSpendPoints();
                    $stepsSecond = floor($rulePointsNumber / $rule->getSpendPoints());
                }

                if ($rulePointsNumber < $rule->getSpendMinAmount($subtotal)) {
                    continue;
                }

                $pointsNumber = $pointsNumber - $rulePointsNumber;

                $stepsFirst = round($subtotal / $rule->getMonetaryStep($subtotal), 2, PHP_ROUND_HALF_DOWN);
                if ($stepsFirst != $subtotal / $rule->getMonetaryStep($subtotal)) {
                    ++$stepsFirst;
                }

                $steps = min($stepsFirst, $stepsSecond);

                $amount = $steps * $rule->getMonetaryStep($subtotal);
                $amount = min($amount, $subtotal);
                $totalAmount += $amount;

                $totalPoints += $rulePointsNumber;

                if ($rule->getIsStopProcessing()) {
                    break;
                }
            }
        }
        $quoteSubTotal = $this->getQuoteSubtotal($quote);
        if ($totalAmount > $quoteSubTotal) {//due to rounding we can have some error
            $totalAmount = $quoteSubTotal;
        }
        $totalAmount = $this->currencyHelper->currency($totalAmount, false, false);

        return new \Magento\Framework\DataObject(['points' => $totalPoints, 'amount' => $totalAmount]);
    }
}
