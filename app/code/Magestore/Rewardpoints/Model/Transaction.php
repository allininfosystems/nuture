<?php
namespace Magestore\Rewardpoints\Model;

class Transaction extends \Magento\Framework\Model\AbstractModel implements \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
{
    /**
     * @var Customer
     */
    protected $rewardAccount;
    /**
     * @var \Magestore\Rewardpoints\Helper\Config
     */
    protected $helper;
    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $helperPoint;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_modelCustomer;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $_rewardpointsHelperCustomer;
    /**
     * @var \Magestore\Rewardpoints\Helper\Action
     */
    protected $_helperAction;
    const STATUS_PENDING                    = 1;
    const STATUS_ON_HOLD                    = 2;
    const STATUS_COMPLETED                  = 3;
    const STATUS_CANCELED                   = 4;
    const STATUS_EXPIRED                    = 5;
    const ACTION_TYPE_BOTH                  = 0;
    const ACTION_TYPE_EARN                  = 1;
    const ACTION_TYPE_SPEND                 = 2;
    const XML_PATH_MAX_BALANCE              = 'rewardpoints/earning/max_balance';
    const XML_PATH_EMAIL_ENABLE             = 'rewardpoints/email/enable';
    const XML_PATH_EMAIL_SENDER             = 'rewardpoints/email/sender';
    const XML_PATH_EMAIL_UPDATE_BALANCE_TPL = 'rewardpoints/email/update_balance';
    const XML_PATH_EMAIL_BEFORE_EXPIRE_TPL  = 'rewardpoints/email/before_expire_transaction';
    const XML_PATH_EMAIL_EXPIRE_DAYS        = 'rewardpoints/email/before_expire_days';

    /**
     * Redefine event Prefix, event object
     *
     * @var string
     */
    protected $_eventPrefix = 'rewardpoints_transaction';
    protected $_eventObject = 'rewardpoints_transaction';

    /**
     * Transaction constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Framework\App\Action\Context $contextAction
     * @param Customer $rewardpointsCustomer
     * @param \Magestore\Rewardpoints\Helper\Config $helperConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magento\Customer\Model\Customer $modelCustomer
     * @param \Magestore\Rewardpoints\Helper\Customer $rewardpointsHelperCustomer
     * @param \Magestore\Rewardpoints\Helper\Action $helperAction
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Action\Context $contextAction,
        \Magestore\Rewardpoints\Model\Customer $rewardpointsCustomer,
        \Magestore\Rewardpoints\Helper\Config $helperConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magento\Customer\Model\Customer $modelCustomer,
        \Magestore\Rewardpoints\Helper\Customer $rewardpointsHelperCustomer,
        \Magestore\Rewardpoints\Helper\Action $helperAction,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->messageManager              = $contextAction->getMessageManager();
        $this->rewardAccount               = $rewardpointsCustomer;
        $this->helper                      = $helperConfig;
        $this->_transportBuilder           = $transportBuilder;
        $this->_storeManager               = $storeManager;
        $this->helperPoint                 = $helperPoint;
        $this->_modelCustomer              = $modelCustomer;
        $this->_rewardpointsHelperCustomer = $rewardpointsHelperCustomer;
        $this->_helperAction               = $helperAction;


    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Rewardpoints\Model\ResourceModel\Transaction');
    }

    /**
     * get transaction status as hash array
     *
     * @return array
     */
    public function getStatusHash()
    {
        return array(
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_ON_HOLD => __('On Hold'),
            self::STATUS_COMPLETED => __('Complete'),
            self::STATUS_CANCELED => __('Canceled'),
            self::STATUS_EXPIRED => __('Expired'),
        );
    }

    public function getStatusArray()
    {
        $options = array();
        foreach ($this->getStatusHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label,
            );
        }
        return $options;
    }

    public function getConst($const)
    {
        $data = array(
            'STATUS_PENDING' => self::STATUS_PENDING,
            'STATUS_ON_HOLD' => self::STATUS_ON_HOLD,
            'STATUS_COMPLETED' => self::STATUS_COMPLETED,
            'STATUS_CANCELED' => self::STATUS_CANCELED,
            'STATUS_EXPIRED' => self::STATUS_EXPIRED,
        );
        if (isset($data[$const]) && $data[$const]) {
            return $data[$const];
        } else {
            return false;
        }
    }

    public function completeTransaction()
    {

        if (!$this->getId() || !$this->getCustomerId() || !$this->getRewardId() || $this->getPointAmount() <= 0 || !in_array($this->getStatus(), array(self::STATUS_PENDING, self::STATUS_ON_HOLD))
        ) {
            $this->messageManager->addError(__('Invalid transaction data to complete.'));
            return false;
        }
        $rewardAccount = $this->getRewardAccount();
        if ($this->getData('status') == self::STATUS_ON_HOLD) {
            $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
        }
        // dispatch event when complete a transaction
        $this->_eventManager->dispatch($this->_eventPrefix . '_complete_' . $this->getData('action'), $this->_getEventData());

        $this->setStatus(self::STATUS_COMPLETED);

        $maxBalance = (int)$this->helper->getConfig(self::XML_PATH_MAX_BALANCE, $this->getStoreId());

        if ($maxBalance > 0 && $this->getRealPoint() > 0 && $rewardAccount->getPointBalance() + $this->getRealPoint() > $maxBalance
        ) {
            if ($maxBalance > $rewardAccount->getPointBalance()) {
                $this->setPointAmount($maxBalance - $rewardAccount->getPointBalance() + $this->getPointAmount() - $this->getRealPoint());
                $this->setRealPoint($maxBalance - $rewardAccount->getPointBalance());
                $rewardAccount->setPointBalance($maxBalance);
                $this->sendUpdateBalanceEmail($rewardAccount);
            } else {
                $this->messageManager->addError(__('Maximum points allowed in account balance is %1.', $maxBalance));
                return false;
            }
        } else {
            $rewardAccount->setPointBalance($rewardAccount->getPointBalance() + $this->getRealPoint());
            $this->sendUpdateBalanceEmail($rewardAccount);
        }

        // Save reward account and transaction to database
        $rewardAccount->save();

        $this->save();
        return $this;
    }

    public function getRewardAccount()
    {
        if (!$this->hasData('reward_account')) {
            $this->setData('reward_account',
                $this->rewardAccount->load($this->getRewardId())
            );
        }
        return $this->getData('reward_account');
    }

    /**
     * send Update Balance to customer
     *
     * @param \Magestore\RewardPoints\Model\Customer $rewardAccount
     * @return \Magestore\RewardPoints\Model\Transaction
     */
    public function sendUpdateBalanceEmail($rewardAccount = null)
    {

        if (!$this->helper->getConfig(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
            return $this;
        }

        if (is_null($rewardAccount)) {
            $rewardAccount = $this->getRewardAccount();
        }

        if (!$rewardAccount->getIsNotification()) {
            return $this;
        }

        $customer = $this->getCustomer();
        if (!$customer) {
            $customer = $this->_modelCustomer->load($rewardAccount->getCustomerId());
        }
        if (!$customer->getId()) {
            return $this;
        }


        $store = $this->_storeManager->getStore()->getId();

//        $translate = Mage::getSingleton('core/translate');
//        $translate->setTranslateInline(false);

        $customerName = '';
        if ($customer instanceof \Magento\Customer\Model\Customer) {
            $customerName = $customer->getName();
        } else if ($customer instanceof \Magento\Customer\Model\Data\Customer) {
            if ($customer->getPrefix()) {
                $customerName = $customer->getPrefix() . ' ';
            }
            if ($customer->getFirstname()) {
                $customerName .= $customer->getFirstname() . ' ';
            }
            if ($customer->getMiddlename()) {
                $customerName .= $customer->getMiddlename() . ' ';
            }
            if ($customer->getLastname()) {
                $customerName .= $customer->getLastname();
            }
        }
        $templateId = $this->helper->getConfig(self::XML_PATH_EMAIL_UPDATE_BALANCE_TPL, $store);
        try {
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
                ->setTemplateVars(
                    [
                        'store' => $this->_storeManager->getStore(),
                        'customer' => $customer,
                        'customerName' => $customerName,
                        'title' => $this->getTitle(),
                        'amount' => $this->getPointAmount(),
                        'total' => $rewardAccount->getPointBalance(),
                        'point_amount' => $this->helperPoint->format($this->getPointAmount(), $store),
                        'point_balance' => $this->helperPoint->format($rewardAccount->getPointBalance(), $store),
                        'status' => $this->getStatusLabel(),
                    ]
                )
                ->setFrom($this->helper->getConfig(self::XML_PATH_EMAIL_SENDER, $store))
                ->addTo($customer->getEmail(), $customerName)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this;
        }
//      $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * get status label of transaction
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $statushash = $this->getStatusHash();
        if (isset($statushash[$this->getStatus()])) {
            return $statushash[$this->getStatus()];
        }
        return '';
    }


    /**
     * Cancel Transaction, allow for Pending, On Hold and Completed transaction
     * only cancel transaction with amount > 0
     * Cancel mean that similar as we do not have this transaction
     *
     * @return \Magestore\RewardPoints\Model\Transaction
     */
    public function cancelTransaction()
    {
        if (!$this->getId() || !$this->getCustomerId() || !$this->getRewardId() || $this->getPointAmount() <= 0 || $this->getStatus() > self::STATUS_COMPLETED || !$this->getStatus()
        ) {
            $this->messageManager->addError(__('Invalid transaction data to cancel.'));
            return false;
        }

        // dispatch event when complete a transaction
        $this->_eventManager->dispatch($this->_eventPrefix . '_cancel_' . $this->getData('action'), $this->_getEventData());

        if ($this->getStatus() != self::STATUS_COMPLETED) {
            if ($this->getData('status') == self::STATUS_ON_HOLD) {
                $rewardAccount = $this->getRewardAccount();
                $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
                $rewardAccount->save();
            }
            $this->setStatus(self::STATUS_CANCELED);
            $this->save();
            return $this;
        }
        $this->setStatus(self::STATUS_CANCELED);
        $rewardAccount = $this->getRewardAccount();
        if ($rewardAccount->getPointBalance() < $this->getRealPoint()) {
            $this->messageManager->addWarning(__('Account balance is not enough to cancel.'));
            return false;
        }
        $rewardAccount->setPointBalance($rewardAccount->getPointBalance() - $this->getRealPoint());
        $this->sendUpdateBalanceEmail($rewardAccount);

        // Save reward account and transaction to database
        $rewardAccount->save();
        $this->save();

        // Change point used for other transaction
        if ($this->getPointUsed() > 0) {
            $pointAmount = $this->getPointAmount();
            $this->setPointAmount(-$this->getPointUsed());
            $this->_getResource()->updatePointUsed($this);
            $this->setPointAmount($pointAmount);
        }

        return $this;
    }


    /**
     * Expire Transaction, allow for Pending, On Hold and Completed transaction
     * only expire transaction with amount > 0
     *
     * @return \Magestore\RewardPoints\Model\Transaction
     */
    public function expireTransaction()
    {
        if (!$this->getId() || !$this->getCustomerId() || !$this->getRewardId() || $this->getPointAmount() <= $this->getPointUsed() || $this->getStatus() > self::STATUS_COMPLETED || !$this->getStatus() || strtotime($this->getExpirationDate()) > time() || !$this->getExpirationDate()
        ) {
            $this->messageManager->addError(__('Invalid transaction data to expire.'));
            return false;
        }

        // dispatch event when complete a transaction
        $this->_eventManager->dispatch($this->_eventPrefix . '_expire_' . $this->getData('action'), $this->_getEventData());

        if ($this->getStatus() != self::STATUS_COMPLETED) {
            if ($this->getData('status') == self::STATUS_ON_HOLD) {
                $rewardAccount = $this->getRewardAccount();
                $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
                $rewardAccount->save();
            }
            $this->setStatus(self::STATUS_EXPIRED);
            $this->save();
            return $this;
        }

        $this->setStatus(self::STATUS_EXPIRED);
        $rewardAccount = $this->getRewardAccount();
        $rewardAccount->setPointBalance(
            $rewardAccount->getPointBalance() - $this->getPointAmount() + $this->getPointUsed()
        );
        $this->sendUpdateBalanceEmail($rewardAccount);

        // Save reward account and transaction to database
        $rewardAccount->save();
        $this->save();
        return $this;
    }

    /**
     * Validate transaction data and create transaction
     *
     * @param array $data
     * @return \Magestore\RewardPoints\Model\Transaction
     */
    public function createTransaction($data = array())
    {

        $this->addData($data);

        if (!$this->getPointAmount()) {
            // Don't create transaction without point amount
            return $this;
        }
        if ($this->getCustomer()) {
            $rewardAccount = $this->_rewardpointsHelperCustomer->getAccountByCustomer($this->getCustomer());
        } else {
            $rewardAccount = $this->_rewardpointsHelperCustomer->getAccountByCustomerId($this->getCustomerId());
        }

        if (!$rewardAccount->getId()) {
            $rewardAccount->setCustomerId($this->getCustomerId())
                ->setData('point_balance', 0)
                ->setData('holding_balance', 0)
                ->setData('spent_balance', 0)
                ->setData('is_notification', 1)
                ->setData('expire_notification', 1)
                ->save();
        }

        if ($rewardAccount->getPointBalance() + $this->getPointAmount() < 0) {
            //Hai.Tran 18/11/2013 fix refund when balance < refund points
            if (!$this->getData('creditmemo_holding') && $rewardAccount->getHoldingBalance() + $this->getPointAmount() < 0) {
                if ($this->getData('creditmemo_transaction'))
                    $this->messageManager->addError(__('Account balance of Customer is not enough to take points back.'));
                throw new \Exception(
                    __('Account balance is not enough to create this transaction.')
                );
            }
        }

        $this->setData('reward_id', $rewardAccount->getId());
        $this->setData('point_used', 0);

        // Always complete reduce transaction when created
        if ($this->getPointAmount() < 0) {
            if (!$this->getData('status')) {
                $this->setData('status', self::STATUS_COMPLETED);
            }
        } else {
            $this->setData('real_point', $this->getPointAmount());
        }

        // If not set status, set it to Pending
        if (!$this->getData('status')) {
            $this->setData('status', self::STATUS_PENDING);
        }

        // Holding transaction, add holding balance
        if ($this->getData('status') == self::STATUS_ON_HOLD) {
            $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() + $this->getPointAmount());
        }
        // Transaction is spending, add spent balance
        if ($this->getData('action_type') == self::ACTION_TYPE_SPEND) {
            $rewardAccount->setSpentBalance($rewardAccount->getSpentBalance() - $this->getPointAmount());
        }


        // Completed when create transaction
        if ($this->getData('status') == self::STATUS_COMPLETED) {
            //$maxBalance 500
            //$this->getPointAmount() 600
            //$rewardAccount->getPointBalance() 500
            $maxBalance = $this->helper->getConfig(self::XML_PATH_MAX_BALANCE, $this->getStoreId());

            if ($maxBalance > 0 && $this->getPointAmount() > 0 && $rewardAccount->getPointBalance() + $this->getPointAmount() > $maxBalance
            ) {
                if ($maxBalance > $rewardAccount->getPointBalance()) {
                    $this->setPointAmount($maxBalance - $rewardAccount->getPointBalance());
                    $this->setRealPoint($maxBalance - $rewardAccount->getPointBalance());
                    $rewardAccount->setPointBalance($maxBalance);
                    $rewardAccount->save();
                    $this->save();
                    $this->sendUpdateBalanceEmail($rewardAccount);
                } else {
                    return $this;
                }
            } else {

                $rewardAccount->setPointBalance($rewardAccount->getPointBalance() + $this->getPointAmount());
                $rewardAccount->save();
                $this->save();
                $this->sendUpdateBalanceEmail($rewardAccount);
            }
        } else {
            if ($this->getPointAmount() < 0 && $this->getData('status') == self::STATUS_ON_HOLD && $this->getData('action_type') == self::ACTION_TYPE_EARN
            ) {
                $isHoldingStatus = true;
                $this->setData('status', self::STATUS_COMPLETED);
                // Update real points and point used for holding transaction (earning) depend on account/ order
                $this->_getResource()->updateRealPointHolding($this);
            }
            $rewardAccount->save();
            $this->save();
        }

        // Save transactions and customer to Database
        if ($this->getPointAmount() < 0 && empty($isHoldingStatus)) {
            if ($this->getData('action_type') == self::ACTION_TYPE_EARN) {
                // Update real points for transaction depend on account/ order
                $this->_getResource()->updateRealPoint($this);
            }
            // Update other transactions (point_used) depend on Account
            $this->_getResource()->updatePointUsed($this);
        }

        // Dispatch Event when create an action
        $this->_eventManager->dispatch($this->_eventPrefix . '_created_' . $this->getData('action')
            , $this->_getEventData()
        );

        return $this;
    }

    /**
     * get transaction title as HTML
     *
     * @return string
     */
    public function getTitleHtml()
    {

        if ($this->hasData('title') && $this->getData('title') != '') {
            return $this->getData('title');
        }
        try {
            $this->setData('title_html', $this->getActionInstance()->getActionLabel());
        } catch (\Exception $e) {
            $this->setData('title_html', $this->getTitle());
        }
        return $this->getData('title_html');
    }

    /**
     * get action model of current transaction
     *
     * @return \Magestore\Rewardpoints\Model\InterfaceAction
     */
    public function getActionInstance()
    {
        return $this->_helperAction->getActionModel($this->getAction());
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->getData('transaction_id');
    }

    /**
     * @return mixed
     */
    public function getRewardId()
    {
        return $this->getData('reward_id');
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {
        return $this->getData('customer_email');
    }

    /**
     * @return mixed
     */
    public function getCurrentPointBalance()
    {
        return $this->getRewardAccount()->getPointBalance();
    }


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->getData('action');
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    /**
     * @return mixed
     */
    public function getPointAmount()
    {
        return $this->getData('point_amount');
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * @return mixed
     */
    public function getCreatedTime()
    {
        return $this->getData('created_time');
    }

    /**
     * @return mixed
     */
    public function getUpdatedTime()
    {
        return $this->getData('updated_time');
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->getData('expiration_date');
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * @return mixed
     */
    public function getOrderIncrementId()
    {
        return $this->getData('order_increment_id');
    }

    /**
     * @return mixed
     */
    public function getOrderAmount()
    {
        return $this->getData('order_amount');
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->getData('discount');
    }

    /**
     * @return mixed
     */
    public function getExtraContent()
    {
        return $this->getData('extra_content');
    }

    /**
     * @param $transactionId
     * @return mixed
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData('transaction_id', $transactionId);
    }

    /**
     * @param $rewardId
     * @return mixed
     */
    public function setRewardId($rewardId)
    {
        return $this->setData('reward_id', $rewardId);
    }

    /**
     * @param $customerId
     * @return mixed
     */
    public function setCustomerId($customerId)
    {
        return $this->setData('customer_id', $customerId);
    }

    /**
     * @param $customerEmail
     * @return mixed
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData('customer_email', $customerEmail);
    }

    /**
     * @param $balance
     * @return mixed
     */
    public function setCurrentPointBalance($balance)
    {
        return $this->setData('current_point_balance', $balance);
    }

    /**
     * @param $title
     * @return mixed
     */
    public function setTitle($title)
    {
        return $this->setData('title', $title);
    }

    /**
     * @param $action
     * @return mixed
     */
    public function setAction($action)
    {
        return $this->setData('action', $action);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * @param $pointAmount
     * @return mixed
     */
    public function setPointAmount($pointAmount)
    {
        return $this->setData('point_amount', $pointAmount);
    }

    /**
     * @param $status
     * @return mixed
     */
    public function setStatus($status)
    {
        return $this->setData('status', $status);
    }

    /**
     * @param $createdTime
     * @return mixed
     */
    public function setCreatedTime($createdTime)
    {
        return $this->setData('created_time', $createdTime);
    }

    /**
     * @param $time
     * @return mixed
     */
    public function setUpdatedTime($time)
    {
        return $this->setData('updated_time', $time);
    }

    /**
     * @param $time
     * @return mixed
     */
    public function setExpirationDate($time)
    {
        return $this->setData('expiration_date', $time);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function setOrderId($id)
    {
        return $this->setData('order_id', $id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function setOrderIncrementId($id)
    {
        return $this->setData('order_increment_id', $id);
    }

    /**
     * @param $amount
     * @return mixed
     */
    public function setOrderAmount($amount)
    {
        return $this->setData('order_amount', $amount);
    }

    /**
     * @param $discount
     * @return mixed
     */
    public function setDiscount($discount)
    {
        return $this->setData('discount', $discount);
    }

    /**
     * @param $content
     * @return mixed
     */
    public function setExtraContent($content)
    {
        return $this->setData('content', $content);
    }


}

