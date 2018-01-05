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

use Mirasvit\Rewards\Model\Config as Config;
use Mirasvit\Rewards\Model\ResourceModel;
use Magento\Framework\App\State as AppState;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Behavior extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        AppState $appState,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        ResourceModel\Earning\Rule\CollectionFactory $earningRuleCollectionFactory,
        \Mirasvit\Rewards\Model\Earning\Rule\QueueFactory $earningRuleQueueFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
    ) {
        $this->appState                     = $appState;
        $this->customerFactory              = $customerFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->earningRuleCollectionFactory = $earningRuleCollectionFactory;
        $this->earningRuleQueueFactory      = $earningRuleQueueFactory;
        $this->resource                     = $resource;
        $this->rewardsBalance               = $rewardsBalance;
        $this->rewardsData                  = $rewardsData;
        $this->config                       = $config;
        $this->context                      = $context;
        $this->date                         = $date;
        $this->storeManager                 = $storeManager;
        $this->messageManager               = $messageManager;
        $this->customerGroup                = $customerGroup;

        parent::__construct($context);
    }

    /**
     * @param string   $ruleType
     * @param bool|int $customerId
     * @param bool|int $websiteId
     * @param bool     $code
     *
     * @return bool
     */
    public function addToQueueRule($ruleType, $customerId = false, $websiteId = false, $code = false)
    {
        if ($code) {
            $code = $ruleType.'-'.$code;
        } else {
            $code = $ruleType;
        }
        if (!$websiteId) {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }

        if (!$customer = $this->getCustomer($customerId)) {
            return false;
        }

        if (!$this->checkIsAllowToQueue($customer->getId(), $code)) {
            return false;
        }

        if (!$this->checkIsAllowToProcessRule($customer->getId(), $code)) {
            return false;
        }

        $queue = $this->earningRuleQueueFactory->create()
            ->setCustomerId($customer->getId())
            ->setWebsiteId((int)$websiteId)
            ->setRuleType($ruleType)
            ->setRuleCode($code)
            ->save()
        ;

        return (bool)$queue->getId();
    }

    /**
     * @param string   $ruleType
     * @param bool|int $customerId
     * @param bool     $websiteId
     * @param bool     $code
     * @param array    $options
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function processRule($ruleType, $customerId = false, $websiteId = false, $code = false, $options = [])
    {
        if ($code) {
            $code = $ruleType.'-'.$code;
        } else {
            $code = $ruleType;
        }

        if (!$customer = $this->getCustomer($customerId)) {
            return false;
        }

        if (!$this->checkIsAllowToProcessRule($customer->getId(), $code)) {
            return false;
        }

        $rules = $this->getRules($ruleType, $customer, $websiteId, $code);

        $lastTransaction = false;
        foreach ($rules as $rule) {
            /* @var \Mirasvit\Rewards\Model\Earning\Rule $rule */
            $rule->afterLoad();

            $object = new \Magento\Framework\DataObject();
            $object->setCustomer($customer);
            if (isset($options['referred_customer'])) {
                $object->setReferredCustomer($options['referred_customer']);
                $customer->setReferredCustomer($options['referred_customer']);
            }

            if (!$rule->validate($customer)) {
                continue;
            }
            $total = $rule->getEarnPoints();
            if (isset($options['order'])) {
                /** @var \Magento\Sales\Model\Order $order */
                $order = $options['order'];
                switch ($rule->getEarningStyle()) {
                    case Config::EARNING_STYLE_AMOUNT_SPENT:
                        if ($this->config->getGeneralIsIncludeTaxEarning()) {
                            $subtotal = $order->getGrandTotal();
                        } else {
                            $subtotal = $order->getSubtotal();
                        }
                        $steps = (int) ($subtotal / $rule->getMonetaryStep());
                        $amount = $steps * $rule->getEarnPoints();
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total = $amount;
                        break;
                    case Config::EARNING_STYLE_QTY_SPENT:
                        $steps = (int) ($order->getTotalQtyOrdered() / $rule->getQtyStep());
                        $amount = $steps * $rule->getEarnPoints();
                        if ($rule->getPointsLimit() && $amount > $rule->getPointsLimit()) {
                            $amount = $rule->getPointsLimit();
                        }
                        $total = $amount;
                        break;
                }
            }

            if (!$this->isInLimit($rule, $customer->getId(), $total)) {
                continue;
            }

            $lastTransaction = $this->rewardsBalance->changePointsBalance(
                $customer,
                $total,
                $rule->getHistoryMessage(),
                $code.'-'.$rule->getId(),
                true,
                $rule->getEmailMessage()
            );

            // If customer group is set, then customer should be assigned there
            $existingGroups = $this->customerGroup->toOptionArray();
            if ($rule->getTransferToGroup() && ($customer->getGroupId() != $rule->getTransferToGroup())) {
                foreach ($existingGroups as $group) {
                    if ($group['value'] == $rule->getTransferToGroup()) {
                        $customer->setGroupId($rule->getTransferToGroup())
                            ->save();
                        break;
                    }
                }
            }

            if ($lastTransaction) {
                $this->addSuccessMessage($rule->getEarnPoints(), $ruleType);
            }
            if ($rule->getIsStopProcessing()) {
                break;
            }
        }

        return $lastTransaction;
    }

    /**
     * @param string                           $ruleType
     * @param \Magento\Customer\Model\Customer $customer
     * @param bool|int                         $websiteId
     * @param bool|string                      $code
     * @return bool|int
     */
    public function getEstimatedEarnPoints($ruleType, $customer, $websiteId = false, $code = false)
    {
        if (!$this->checkIsAllowToProcessRule($customer->getId(), $code)) {
            return false;
        }
        if ($code) {
            $code = $ruleType.'-'.$code;
        } else {
            $code = $ruleType;
        }

        $rules = $this->getRules($ruleType, $customer, $websiteId, $code);
        $amount = 0;
        foreach ($rules as $rule) {
            if (!$this->isInLimit($rule, $customer->getId(), $rule->getEarnPoints())) {
                continue;
            }
            $amount += $rule->getEarnPoints();
        }

        return $amount;
    }

    /**
     * @param int    $customerId
     * @param string $code
     * @return bool
     */
    protected function checkIsAllowToQueue($customerId, $code)
    {
        $collection = $this->earningRuleQueueFactory->create()->getCollection()
            ->addFieldToFilter('rule_code', $code)
            ->addFieldToFilter('customer_id', $customerId);
        $isAllow = $collection->count() == 0;

        return $isAllow;
    }

    /**
     * @param int    $customerId
     * @param string $code
     * @return bool
     */
    protected function checkIsAllowToProcessRule($customerId, $code)
    {
        $collection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('code', $code)
            ->addFieldToFilter('customer_id', $customerId);
        $isAllow = $collection->count() == 0;

        return $isAllow;
    }

    /**
     * @param string                           $ruleType
     * @param \Magento\Customer\Model\Customer $customer
     * @param bool|int                         $websiteId
     * @return \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Collection
     */
    protected function getRules($ruleType, $customer, $websiteId = false)
    {
        if (!$websiteId) {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }
        $customerGroupId = $customer->getGroupId();
        $rules = $this->earningRuleCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addCustomerGroupFilter($customerGroupId)
            ->addIsActiveFilter()
            ->addCurrentFilter()
            ->addFieldToFilter('type', \Mirasvit\Rewards\Model\Earning\Rule::TYPE_BEHAVIOR)
            ->addFieldToFilter('behavior_trigger', $ruleType);

        return $rules;
    }

    /**
     * @param \Magento\Rule\Model\AbstractModel $rule
     * @param int                               $customerId
     * @param int                               $futurePoints
     * @return bool
     */
    protected function isInLimit($rule, $customerId, $futurePoints)
    {
        if (!$rule->getPointsLimit()) {
            return true;
        }
        $resource = $this->resource;
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('mst_rewards_transaction');
        $date = $this->date->gmtDate('Y-m-d 00:00:00');
        $sum = (int) $readConnection->fetchOne(
            "SELECT SUM(amount) FROM $table ".
            "WHERE customer_id=".(int) $customerId." AND ".
            "code LIKE '{$rule->getBehaviorTrigger()}-%' AND created_at > '$date'"
        );
        if ($rule->getPointsLimit() >= ($sum + $futurePoints)) {
            return true;
        }

        return false;
    }

    /**
     * @param bool|int $customerId
     * @return null|\Magento\Customer\Model\Customer
     */
    protected function getCustomer($customerId)
    {
        if (is_object($customerId)) {
            $customerId = $customerId->getId();
        }
        if (!$customerId && $this->appState->getAreaCode() == 'frontend') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->get('Magento\Customer\Model\Session');
            $customerId = $customerSession->getCustomerId();
            if (!$customerId) {
                return null;
            }
        }
        if ($customerId) {
            $customer = $this->customerFactory->create()->load($customerId);
            return $customer;
        }
    }

    /**
     * Adds a success message in the frontend (via session).
     *
     * @param int    $points
     * @param string $ruleType
     *
     * @return void
     */
    protected function addSuccessMessage($points, $ruleType)
    {
        $comments = [
            Config::BEHAVIOR_TRIGGER_SIGNUP => __('You received %1 for signing up'),
            Config::BEHAVIOR_TRIGGER_SEND_LINK => __('You received %1 for sending this product'),
            Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP => __('You received %1 for sign up for newsletter'),
            Config::BEHAVIOR_TRIGGER_PUSHNOTIFICATION_SIGNUP => __(
                'You received %1 for sign up for push notifications'
            ),
            Config::BEHAVIOR_TRIGGER_TAG => __('You will receive %1 after approving of this tag'),
            Config::BEHAVIOR_TRIGGER_REVIEW => __('You will receive %1 after approving of this review'),
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP => __(
                'You received %1 for sign up of referral customer.'
            ),
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER => __('You received %1 for order of referral customer.'),
            Config::BEHAVIOR_TRIGGER_BIRTHDAY => __('Happy birthday! You received %1.'),
        ];
        $hiddenPoints = [
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP,
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER,
        ];
        if (isset($comments[$ruleType])) {
            $notification = __($comments[$ruleType], $this->rewardsData->formatPoints($points));
            if (!in_array($ruleType, $hiddenPoints)) {
                $this->messageManager->addSuccess($notification);
            }
        }
    }
}
