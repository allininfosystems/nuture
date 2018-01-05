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

class Balance extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Rewards\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Mirasvit\Rewards\Helper\Mail
     */
    protected $rewardsMail;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Model\TransactionFactory                          $transactionFactory
     * @param \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection                           $resource
     * @param \Mirasvit\Rewards\Helper\Mail                                       $rewardsMail
     * @param \Magento\Framework\App\Helper\Context                               $context
     */
    public function __construct(
        \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory,
        \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mirasvit\Rewards\Helper\Mail $rewardsMail,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->resource = $resource;
        $this->rewardsMail = $rewardsMail;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * Change the number of points on the customer balance.
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param int                              $pointsNum
     * @param string                           $historyMessage
     * @param bool                             $code - if we have code, we will check for uniqness this transaction
     * @param bool                             $notifyByEmail
     * @param bool                             $emailMessage
     *
     * @return bool
     */
    public function changePointsBalance(
        $customer, $pointsNum, $historyMessage, $code = false, $notifyByEmail = false, $emailMessage = false
    ) {
        if (is_object($customer)) {
            $customer = $customer->getId();
        }
        if ($code) {
            $collection = $this->transactionCollectionFactory->create()
                            ->addFieldToFilter('customer_id', $customer)
                            ->addFieldToFilter('code', $code);
            if ($collection->count()) {
                return false;
            }
        }
        $transaction = $this->transactionFactory->create()
            ->setCustomerId($customer)
            ->setAmount($pointsNum);
        if ($code) {
            $transaction->setCode($code);
        }
        $historyMessage = $this->rewardsMail->parseVariables($historyMessage, $transaction);
        $transaction->setComment($historyMessage);
        $transaction->save();
        if ($notifyByEmail) {
            $this->rewardsMail->sendNotificationBalanceUpdateEmail($transaction, $emailMessage);
        }

        return $transaction;
    }

    /**
     * @param \Magento\Customer\Model\Customer|int $customer
     * @return int
     */
    public function getBalancePoints($customer)
    {
        if (is_object($customer)) {
            $customer = $customer->getId();
        }
        $resource = $this->resource;
        $table = $resource->getTableName('mst_rewards_transaction');

        return (int)$resource->getConnection()->fetchOne(
            "SELECT SUM(amount) FROM $table WHERE customer_id=?", [(int)$customer]
        );
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param string                           $code
     * @return bool
     */
    public function cancelEarnedPoints($customer, $code)
    {
        if (!$earnedTransaction = $this->getEarnedPointsTransaction($customer, $code)) {
            return false;
        }
        $earnedTransaction->delete();
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param string                           $code
     * @return \Mirasvit\Rewards\Model\Transaction
     */
    public function getEarnedPointsTransaction($customer, $code)
    {
        $collection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addFieldToFilter('code', $code)
        ;
        if ($collection->count()) {
            return $collection->getFirstItem();
        }
    }
}
