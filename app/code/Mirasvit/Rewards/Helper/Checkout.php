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



namespace Mirasvit\Rewards\Helper;

class Checkout extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    protected $rewardsPurchase;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Magento\Checkout\Model\Cart          $cart
     * @param \Mirasvit\Rewards\Helper\Purchase     $rewardsPurchase
     * @param \Mirasvit\Rewards\Helper\Data         $rewardsData
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->cart = $cart;
        $this->rewardsPurchase = $rewardsPurchase;
        $this->rewardsData = $rewardsData;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Checkout\Model\Cart
     */
    protected function _getCart()
    {
        return $this->cart;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->context->getRequest();
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    public function processAdminRequest($quote)
    {
        $purchase = $this->rewardsPurchase->getByQuote($quote);

        return $this->process($purchase);
    }

    /**
     * @param string $paymentMethod
     * @return array
     */
    public function updatePaymentMethod($paymentMethod)
    {
        $response = [
            'success' => false,
            'points'  => false,
        ];
        /** $var \Mirasvit\Rewards\Model\Purchase $purchase */
        if (($purchase = $this->rewardsPurchase->getPurchase()) && $purchase->getQuote()->getCustomerId()) {
            $quote = $purchase->getQuote();
            if ($quote->getItemVirtualQty() > 0) {
                $quote->getBillingAddress()->setPaymentMethod($paymentMethod);
            } else {
                $quote->getShippingAddress()->setPaymentMethod($paymentMethod);
            }

            $purchase->refreshPointsNumber(true);
            $response['success'] = (bool)$purchase->getEarnPoints();
            $response['points']  = $this->rewardsData->formatPoints($purchase->getEarnPoints());

            $response['chechoutRewardsPointsSpend'] = 0;
            $response['chechoutRewardsPointsUsed']  = 0;
            if ($point = $purchase->getSpendPoints()) {
                $response['chechoutRewardsPointsSpend'] = $this->rewardsData->formatPoints($point);
                $response['chechoutRewardsPointsUsed']  = $point;
            }
            $quote = $purchase->getQuote();
            $response['chechoutRewardsPointsAvailble'] = $this->rewardsData->formatPoints(
                $purchase->getCustomerBalancePoints($quote->getCustomerId())
            );
            $response['chechoutRewardsPointsMax'] = $purchase->getMaxPointsNumberToSpent();
            $response['chechoutRewardsIsShow']    = (bool)$response['chechoutRewardsPointsMax'];
        }

        return $response;
    }

    /**
     * @return array
     */
    public function processRequest()
    {
        $response = [
            'success' => false,
            'message' => false,
        ];
        /*
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            return $response;
        }
        $purchase = $this->rewardsPurchase->getPurchase();

        return $this->process($purchase);
    }

    /**
     * @param \Mirasvit\Rewards\Model\Purchase $purchase
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function process($purchase)
    {
        $response = [
            'success' => false,
            'message' => false,
        ];
        $pointsNumber = abs((int) $this->getRequest()->getParam('points_amount'));
        if ($this->getRequest()->getParam('remove-points') == 1) {
            $pointsNumber = false;
        }
        $oldPointsNumber = $purchase->getSpendPoints();

        if (!$pointsNumber && !$oldPointsNumber) {
            return $response;
        }

        try {
            $purchase->setSpendPoints($pointsNumber)
                ->refreshPointsNumber(true)
                ->save();
            if ($pointsNumber) {
                $response['success'] = true;
                $response['message'] = __(
                    '%1 was applied.', $this->rewardsData->formatPoints($purchase->getSpendPoints())
                );
                if ($pointsNumber != $purchase->getSpendPoints()) {
                    if ($pointsNumber < $purchase->getSpendMinPoints()) {
                        $response['success'] = false;
                        $response['message'] = __(
                            'Minimum number is %1.', $this->rewardsData->formatPoints($purchase->getSpendMinPoints())
                        );
                    } elseif ($pointsNumber > $purchase->getSpendMaxPoints()) {
                        $response['success'] = false;
                        $response['message'] = __(
                            'Maximum number is %1.', $this->rewardsData->formatPoints($purchase->getSpendMaxPoints())
                        );
                    }
                }
            } else {
                $response['success'] = true;
                $response['message'] = __('%1 was canceled.', $this->rewardsData->getPointsName());
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = __('Cannot apply %1.', $this->rewardsData->getPointsName());
            $this->context->getLogger()->errorException($e);
        }
        $response['spend_points'] = $purchase->getSpendPoints();
        $response['spend_points_formated'] = $this->rewardsData->formatPoints($purchase->getSpendPoints());

        return $response;
    }
}
