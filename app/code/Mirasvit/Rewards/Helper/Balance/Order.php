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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Purchase\CollectionFactory
     */
    protected $purchaseCollectionFactory;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    protected $rewardsPurchase;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Earn
     */
    protected $rewardsBalanceEarn;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    protected $rewardsBalance;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Magento\Store\Model\App\Emulation                                  $appEmulation
     * @param \Magento\Store\Model\StoreFactory                                   $storeFactory
     * @param \Mirasvit\Rewards\Model\ResourceModel\Purchase\CollectionFactory    $purchaseCollectionFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory          $quoteCollectionFactory
     * @param \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Mirasvit\Rewards\Helper\Purchase                                   $rewardsPurchase
     * @param \Mirasvit\Rewards\Helper\Balance\Earn                               $rewardsBalanceEarn
     * @param \Mirasvit\Rewards\Helper\Balance                                    $rewardsBalance
     * @param \Mirasvit\Rewards\Helper\Data                                       $rewardsData
     * @param \Magento\Framework\App\Helper\Context                               $context
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Mirasvit\Rewards\Model\ResourceModel\Purchase\CollectionFactory $purchaseCollectionFactory,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Balance\Earn $rewardsBalanceEarn,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->appEmulation                 = $appEmulation;
        $this->storeFactory                 = $storeFactory;
        $this->purchaseCollectionFactory    = $purchaseCollectionFactory;
        $this->quoteCollectionFactory       = $quoteCollectionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->rewardsPurchase              = $rewardsPurchase;
        $this->rewardsBalanceEarn           = $rewardsBalanceEarn;
        $this->rewardsBalance               = $rewardsBalance;
        $this->rewardsData                  = $rewardsData;
        $this->context                      = $context;

        parent::__construct($context);
    }

    /**
     * Returns store ID, bounded with current order. If order is not defined, returns current store.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStoreByOrder($order)
    {
        return ($order) ?
            $this->storeFactory->create()->load($order->getStoreId()) :
            $this->rewardsData->getCurrentStore();
    }

    /**
     * Calculates and adds points, based on order items and subtotal.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     */
    public function earnOrderPoints($order)
    {
        if ($this->getEarnedPointsTransaction($order)) {
            return false;
        }

        $collection = $this->purchaseCollectionFactory->create()
            ->addFieldToFilter('quote_id', $order->getQuoteId());
        if ($collection->count()) { //we have new version of data in DB
            $purchase = $this->rewardsPurchase->getByOrder($order);
            $totalPoints = $purchase->getEarnPoints();
        } else { //we need this for compability with older versions.
            $collection = $this->quoteCollectionFactory->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('entity_id', $order->getQuoteId());
            $quote = $collection->getFirstItem();
            $totalPoints = $this->rewardsBalanceEarn->getPointsEarned($quote);
        }
        if ($totalPoints) {
            $this->rewardsData->setCurrentStore($this->getStoreByOrder($order));
            $this->rewardsBalance->changePointsBalance($order->getCustomerId(), $totalPoints,
                $this->translateComment(
                    $order->getStore()->getId(),
                    'Earned %1 for the order #%2.',
                    $this->rewardsData->formatPoints($totalPoints),
                    $order->getIncrementId()
                ),
                'order_earn-'.$order->getId(), true);

            return $totalPoints;
        }
    }

    /**
     * @return string
     */
    protected function translateComment()
    {
        $args = func_get_args();

        $storeId = $args[0];
        unset($args[0]);

        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);

        $message = call_user_func_array('__', $args)->render();

        $this->appEmulation->stopEnvironmentEmulation();

        return $message;
    }

    /**
     * Cancels earned points.
     *
     * @param \Magento\Sales\Model\Order            $order
     * @param \Magento\Sales\Model\Order\Creditmemo $creditMemo
     *
     * @return bool
     */
    public function cancelEarnedPoints($order, $creditMemo)
    {
        if (!$earnedTransaction = $this->getEarnedPointsTransaction($order)) {
            return false;
        }
        $proportion = $creditMemo->getSubtotal() / $order->getSubtotal();
        if ($proportion > 1) {
            $proportion = 1;
        }
        $creditMemoId = $order->getCreditmemosCollection()->count();
        $totalPoints = round($earnedTransaction->getAmount() * $proportion);
        $this->rewardsData->setCurrentStore($this->getStoreByOrder($order));
        $this->rewardsBalance->changePointsBalance($order->getCustomerId(), -$totalPoints,
            $this->translateComment(
                $order->getStore()->getId(),
                'Cancel earned %1 for the order #%2.',
                $this->rewardsData->formatPoints($totalPoints),
                $order->getIncrementId()
            ),
            'order_earn_cancel-'.$order->getId().'-'.$creditMemoId, false);
    }

    /**
     * Decreases the number of points on the customer account.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    public function spendOrderPoints($order)
    {
        if (!$purchase = $this->rewardsPurchase->getByOrder($order)) {
            return 0;
        }
        if ($totalPoints = $purchase->getSpendPoints()) {
            $this->rewardsData->setCurrentStore($this->getStoreByOrder($order));
            $this->rewardsBalance->changePointsBalance($order->getCustomerId(), -$totalPoints,
                $this->translateComment(
                    $order->getStore()->getId(),
                    'Spent %1 for the order #%2.',
                    $this->rewardsData->formatPoints($totalPoints),
                    $order->getIncrementId()
                ),
                'order_spend-'.$order->getId(), false);

                return $totalPoints;
        }
    }

    /**
     * @param \Magento\Sales\Model\Order                 $order
     * @param \Magento\Sales\Model\Order\Creditmemo|bool $creditMemo
     *
     * @return void
     */
    public function restoreSpendPoints($order, $creditMemo = false)
    {
        if (!$spendTransaction = $this->getSpendPointsTransaction($order)) {
            return;
        }
        if ($creditMemo) { //if we create a credit memo
            $creditMemoId = $order->getCreditmemosCollection()->count();
            $proportion = $creditMemo->getSubtotal() / $order->getSubtotal();
            if ($proportion > 1) {
                $proportion = 1;
            }
            $totalPoints = round($spendTransaction->getAmount() * $proportion);
        } else { //if we cancel order via backend
            $creditMemoId = 0;
            $totalPoints  = $spendTransaction->getAmount();
        }
        $this->rewardsData->setCurrentStore($this->getStoreByOrder($order));
        $this->rewardsBalance->changePointsBalance($order->getCustomerId(), -$totalPoints,
            $this->translateComment(
                $order->getStore()->getId(),
                'Restore spent %1 for the order #%2.',
                $this->rewardsData->formatPoints($totalPoints),
                $order->getIncrementId()
            ),
            'order_spend_restore-'.$order->getId().'-'.$creditMemoId, false);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Mirasvit\Rewards\Model\Transaction
     */
    protected function getEarnedPointsTransaction($order)
    {
        $collection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('code', "order_earn-{$order->getId()}")
        ;
        if ($collection->count()) {
            return $collection->getFirstItem();
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Mirasvit\Rewards\Model\Transaction
     */
    protected function getSpendPointsTransaction($order)
    {
        $collection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('code', "order_spend-{$order->getId()}")
        ;
        if ($collection->count()) {
            return $collection->getFirstItem();
        }
    }
}
