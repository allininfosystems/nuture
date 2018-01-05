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



namespace Mirasvit\Rewards\Model;

use Abraham\TwitterOAuth\TwitterOAuth;
use Mirasvit\Rewards\Model\ResourceModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Cron
{
    /**
     * @var null
     */
    protected $_lockFile = null;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory
     */
    protected $earningRuleCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Queue\CollectionFactory
     */
    protected $earningRuleQueueCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    protected $rewardsBalance;

    /**
     * @var \Mirasvit\Rewards\Helper\Mail
     */
    protected $rewardsMail;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
     * @param ResourceModel\Transaction\CollectionFactory                      $transactionCollectionFactory
     * @param ResourceModel\Earning\Rule\CollectionFactory                     $earningRuleCollectionFactory
     * @param ResourceModel\Earning\Rule\Queue\CollectionFactory               $earningRuleQueueCollectionFactory
     * @param \Mirasvit\Rewards\Model\Config                                   $config
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                      $date
     * @param \Magento\Framework\App\ResourceConnection                        $resource
     * @param \Mirasvit\Rewards\Helper\Balance                                 $rewardsBalance
     * @param \Mirasvit\Rewards\Helper\Mail                                    $rewardsMail
     * @param \Mirasvit\Rewards\Helper\Behavior                                $rewardsBehavior
     * @param \Magento\Framework\Model\Context                                 $context
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        ResourceModel\Earning\Rule\CollectionFactory $earningRuleCollectionFactory,
        ResourceModel\Earning\Rule\Queue\CollectionFactory $earningRuleQueueCollectionFactory,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Mirasvit\Rewards\Helper\Mail $rewardsMail,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\Context $context
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->earningRuleCollectionFactory = $earningRuleCollectionFactory;
        $this->earningRuleQueueCollectionFactory = $earningRuleQueueCollectionFactory;
        $this->config = $config;
        $this->date = $date;
        $this->resource = $resource;
        $this->rewardsBalance = $rewardsBalance;
        $this->rewardsMail = $rewardsMail;
        $this->rewardsBehavior = $rewardsBehavior;
        $this->filesystem = $filesystem;
        $this->context = $context;
    }

    /**
     * @return void
     */
    public function run()
    {
        if (!$this->isLocked()) {
            $this->lock();

            $this->calculateUsedPoints();
            $this->expirePoints();
            $this->sendPointsExpireEmail();
            $this->earnBirthdayPoints();
            $this->earnMilestonePoints();

            $this->unlock();
        }
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        $fp = $this->_getLockFile();
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            flock($fp, LOCK_UN);

            return false;
        }

        return true;
    }

    /**
     * @return object
     */
    public function lock()
    {
        flock($this->_getLockFile(), LOCK_EX | LOCK_NB);

        return $this;
    }

    /**
     * Разлочит файл.
     *
     * @return object
     */
    public function unlock()
    {
        flock($this->_getLockFile(), LOCK_UN);

        return $this;
    }

    /**
     * @return resource
     */
    protected function _getLockFile()
    {
        if ($this->_lockFile === null) {
            $varDir = $this->filesystem
                ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::TMP)
                ->getAbsolutePath();
            if (!file_exists($varDir)) {
                @mkdir($varDir, 0777, true);
            }
            $file = $varDir . '/rewards.lock';

            if (is_file($file)) {
                $this->_lockFile = fopen($file, 'w');
            } else {
                $this->_lockFile = fopen($file, 'x');
            }
            fwrite($this->_lockFile, date('r'));
        }

        return $this->_lockFile;
    }

    /**
     * @param string|bool $now
     * @return void
     */
    public function calculateUsedPoints($now = false)
    {
        if (!$now) {
            $now = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        //get collection of spend transactions
        $spendTransactions = $this->transactionCollectionFactory->create();
        $spendTransactions->getSelect()
            ->where('amount < 0 AND (abs(amount) > abs(amount_used) OR amount_used IS NULL)');

        foreach ($spendTransactions as $spend) {
            if (strpos($spend->getCode(), 'expired-') !== false) {
                continue;
            }
            $earnTransactions = $this->transactionCollectionFactory->create()
                    ->addFieldToFilter('is_expired', 0)
                    ->addFieldToFilter('amount', ['gt' => 0]);
            $earnTransactions->getSelect()
            //                ->where('expires_at > "'.$now.'" OR expires_at IS NULL')
                  ->where('amount > amount_used OR amount_used IS NULL');

            //get collection of earn transactions before current spend transaction
            $earnTransactions->addFieldToFilter('customer_id', $spend->getCustomerId())
                ->addFieldToFilter('main_table.created_at', ['lt' => $spend->getCreatedAt()])
                ->setOrder('created_at', 'asc');

            $spendAmount = abs($spend->getAmount());
            foreach ($earnTransactions as $earn) {
                $avaliablePoints = $earn->getAmount() - $earn->getAmountUsed();
                if ($avaliablePoints >= $spendAmount) {
                    $earn->setAmountUsed($earn->getAmountUsed() + $spendAmount);
                    $spend->setAmountUsed($spend->getAmountUsed() + $spendAmount);
                } else {
                    $earn->setAmountUsed($earn->getAmountUsed() + $avaliablePoints);
                    $spend->setAmountUsed($spend->getAmountUsed() + $avaliablePoints);
                }
                $earn->save();
                $spend->save();

                $spendAmount -= $avaliablePoints;
                if ($spendAmount <= 0) {
                    break;
                }
            }
        }
    }

    /**
     * @param bool|string $now
     * @return void
     */
    public function expirePoints($now = false)
    {
        if (!$now) {
            $now = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }

        $transactions = $this->transactionCollectionFactory->create()
                ->addFieldToFilter('is_expired', 0);
        $transactions->getSelect()->where('expires_at < "'.$now.'"')
                                  ->where('amount > amount_used OR amount_used IS NULL');

        foreach ($transactions as $transaction) {
            $this->rewardsBalance->changePointsBalance(
                $transaction->getCustomerId(),
                -abs($transaction->getAmount() - $transaction->getAmountUsed()),
                __('Transaction #%1 is expired', $transaction->getId()),
                'expired-'.$transaction->getId()
            );
            $transaction->setIsExpired(true)
                        ->save();
        }
    }

    /**
     * @return void
     */
    public function sendPointsExpireEmail()
    {
        $config = $this->config;
        if ($config->getNotificationPointsExpireEmailTemplate() == 'none') {
            return;
        }
        $days = $config->getNotificationSendBeforeExpiringDays();
        $transactions = $this->transactionCollectionFactory->create()
                ->addFieldToFilter('expires_at', ['lt' => $this->date->gmtDate(null, time() + 60 * 60 * 24 * $days)])
                ->addFieldToFilter('is_expired', false)
                ->addFieldToFilter('is_expiration_email_sent', false);
        $transactions->getSelect()->where('amount > amount_used OR amount_used IS NULL');

        foreach ($transactions as $transaction) {
            $this->rewardsMail->sendNotificationPointsExpireEmail($transaction);
            $transaction->setIsExpirationEmailSent(true)
                        ->save();
        }
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function earnBirthdayPoints()
    {
        $customers = $this->customerCollectionFactory->create()
            ->joinAttribute('dob', 'customer/dob', 'entity_id');
        $customers->getSelect()->where('extract(month from `at_dob`.`dob`) = ?', $this->date->date('m'))
            ->where('extract(day from `at_dob`.`dob`) = ?', $this->date->date('d'));
        foreach ($customers as $customer) {
            $this->rewardsBehavior->processRule(
                Config::BEHAVIOR_TRIGGER_BIRTHDAY,
                $customer,
                $customer->getWebsiteId(),
                $this->date->date('Y')
            );
        }
    }

    /**
     * @return void
     */
    public function earnMilestonePoints()
    {
        $resource = $this->resource;
        $connection = $resource->getConnection('core_write');

        $rules = $this->earningRuleCollectionFactory->create()
                    ->addIsActiveFilter()
                    ->addFieldToFilter('behavior_trigger', Config::BEHAVIOR_TRIGGER_INACTIVITY);
        foreach ($rules as $rule) {
            $rule->afterLoad();
            $customers = $this->customerCollectionFactory->create()
                            ->addFieldToFilter('website_id', $rule->getWebsiteIds())
                            ->addFieldToFilter('group_id', $rule->getCustomerGroupIds());
            switch ($rule->getType()) {
                case \Mirasvit\Rewards\Model\Earning\Rule::TYPE_BEHAVIOR:
                    /** @var \Magento\Customer\Model\Customer $customer */
                    foreach ($customers as $customer) {
                        $query = "SELECT DATEDIFF(NOW(), last_visit_at)
                        FROM {$resource->getTableName('customer_visitor')} cv
                        WHERE customer_id={$customer->getId()} order by visitor_id desc LIMIT 1";
                        $daysFromLastVisit = $connection->fetchOne($query);
                        if ($daysFromLastVisit > $rule->getDataByKey('param1')) {
                            $this->rewardsBehavior->processRule(
                                Config::BEHAVIOR_TRIGGER_INACTIVITY,
                                $customer->getId(),
                                $customer->getWebsiteId(),
                                $rule->getId()
                            );
                        }
                    }
                    break;
            }
        }
    }
}
