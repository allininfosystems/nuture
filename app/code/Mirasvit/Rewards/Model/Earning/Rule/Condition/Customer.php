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



namespace Mirasvit\Rewards\Model\Earning\Rule\Condition;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Customer extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Sale\CollectionFactory
     */
    protected $saleCollectionFactory;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    protected $reviewCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory
     */
    protected $referralCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;
    /**
     * @var \Magento\Rule\Model\Condition\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection           $groupCollection
     * @param \Magento\Newsletter\Model\SubscriberFactory                      $subscriberFactory
     * @param \Magento\Sales\Model\ResourceModel\Sale\CollectionFactory        $saleCollectionFactory
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory     $reviewCollectionFactory
     * @param \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory $referralCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory       $orderCollectionFactory
     * @param \Mirasvit\Rewards\Model\Config                                   $config
     * @param \Magento\Rule\Model\Condition\Context                            $context
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource          $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb                    $resourceCollection
     * @param array                                                            $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $groupCollection,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Sales\Model\ResourceModel\Sale\CollectionFactory $saleCollectionFactory,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory $referralCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->groupCollection           = $groupCollection;
        $this->subscriberFactory         = $subscriberFactory;
        $this->saleCollectionFactory     = $saleCollectionFactory;
        $this->reviewCollectionFactory   = $reviewCollectionFactory;
        $this->referralCollectionFactory = $referralCollectionFactory;
        $this->orderCollectionFactory    = $orderCollectionFactory;
        $this->config                    = $config;
        $this->context                   = $context;
        $this->registry                  = $registry;
        $this->resource                  = $resource;
        $this->resourceCollection        = $resourceCollection;

        parent::__construct($context, $data);
    }

    const OPTION_IS_REFEREE                             = 'is_referee';
    const OPTION_IS_REFERRAL                            = 'is_referral';
    const OPTION_GROUP_ID                               = 'group_id';
    const OPTION_ORDERS_SUM                             = 'orders_sum';
    const OPTION_ORDERS_NUMBER                          = 'orders_number';
    const OPTION_IS_SUBSCRIBER                          = 'is_subscriber';
    const OPTION_REVIEWS_NUMBER                         = 'reviews_number';
    const OPTION_REFERRED_SIGNUPS_NUMBER                = 'referred_signups_number';
    const OPTION_REFERRED_ORDERS_NUMBER                 = 'referred_orders_number';
    const OPTION_REFERRED_ORDERS_SUM                    = 'referred_orders_sum';
    const OPTION_REFERRED_NUMBER__ORDERED_AT_LEAST_ONCE = 'referred_ordered_number_at_least_once';

    const OPTION_REFERRAL_IS_REFERRAL    = 'referral_is_referral';
    const OPTION_REFERRAL_GROUP_ID       = 'referral_group_id';
    const OPTION_REFERRAL_ORDERS_SUM     = 'referral_orders_sum';
    const OPTION_REFERRAL_ORDERS_NUMBER  = 'referral_orders_number';
    const OPTION_REFERRAL_IS_SUBSCRIBER  = 'referral_is_subscriber';
    const OPTION_REFERRAL_REVIEWS_NUMBER = 'referral_reviews_number';

    /**
     * @return \Mirasvit\Rewards\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            self::OPTION_IS_REFEREE                             => __('Is Referee'),
            self::OPTION_IS_REFERRAL                            => __('Is Referral'),
            self::OPTION_GROUP_ID                               => __('Group'),
            self::OPTION_ORDERS_SUM                             => __('Lifetime Sales'),
            self::OPTION_ORDERS_NUMBER                          => __('Number of Orders'),
            self::OPTION_IS_SUBSCRIBER                          => __('Is subscriber of newsletter'),
            self::OPTION_REVIEWS_NUMBER                         => __('Number of reviews'),
            self::OPTION_REFERRED_SIGNUPS_NUMBER                => __('Number of referred friends signups'),
            self::OPTION_REFERRED_ORDERS_NUMBER                 => __('Number of referred friends orders'),
            self::OPTION_REFERRED_ORDERS_SUM                    => __('Sum of referred friends orders'),
            self::OPTION_REFERRED_NUMBER__ORDERED_AT_LEAST_ONCE => __('Number of referrals who ordered at least once'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        $type = 'string';

        switch ($this->getAttribute()) {
            case self::OPTION_GROUP_ID:
            case self::OPTION_REFERRAL_GROUP_ID:
                $type = 'multiselect';
                break;

            case self::OPTION_IS_SUBSCRIBER:
                $type = 'select';
                break;
        }

        return $type;
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        $type = 'text';

        switch ($this->getAttribute()) {
            case self::OPTION_GROUP_ID:
            case self::OPTION_REFERRAL_GROUP_ID:
                $type = 'multiselect';
                break;

            case self::OPTION_IS_SUBSCRIBER:
                $type = 'select';
                break;

            case self::OPTION_IS_REFEREE:
                $type = 'select';
                break;
        }

        return $type;
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        $opt = parent::getValueSelectOptions();

        return array_merge($opt, $this->_prepareValueOptions());
    }

    /**
     * @return $this
     */
    protected function _prepareValueOptions()
    {
        $selectOptions = [];

        if (
            $this->getAttribute() === self::OPTION_GROUP_ID ||
            $this->getAttribute() === self::OPTION_REFERRAL_GROUP_ID
        ) {
            $selectOptions = $this->groupCollection->toOptionArray();

            array_unshift($selectOptions, ['value' => 0, 'label' => __('Not registered')]);
        }

        if ($this->getAttribute() === self::OPTION_IS_SUBSCRIBER ||
            $this->getAttribute() === self::OPTION_IS_REFEREE) {
            $selectOptions = [
                ['value' => 0, 'label' => __('No')],
                ['value' => 1, 'label' => __('Yes')],
            ];
        }

        return $selectOptions;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getEntityId()) { //we don't check referred customers
            return true;
        }

        $this->setReferredAttributes($object);
        $result = $this->validateCustomer($object);

        return $result;
    }

    /**
     * @param \Magento\Framework\DataObject $customer - can be registered customer and guest(!)
     *
     * @return bool
     */
    protected function validateCustomer(\Magento\Framework\DataObject $customer)
    {
        $attrCode = $this->getAttribute();

        $subscriber = $this->subscriberFactory->create()->loadByEmail($customer->getEmail());

        $reviews = $this->reviewCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId());
        $reviewsCount = $reviews->count();

        $lifetimeSales = $this->getSumOfOrdersByCustomer($customer);
        $numberOfOrders = $this->getNumberOfOrdersByCustomer($customer);

        $customer
            ->setData(self::OPTION_IS_SUBSCRIBER, $subscriber->getId() ? 1 : 0)
            ->setData(self::OPTION_REVIEWS_NUMBER, $reviewsCount)
            ->setData(self::OPTION_ORDERS_SUM, $lifetimeSales)
            ->setData(self::OPTION_ORDERS_NUMBER, $numberOfOrders);

        $value = $customer->getData($attrCode);
        $res = $this->validateAttribute($value);

        return $res;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function setReferredAttributes($customer)
    {
        $referrals = $this->referralCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addFieldToFilter('new_customer_id', ['neq' => null]);
        $customer->setData('referred_signups_number', $referrals->count());

        $numberOfOrders = 0;
        $numberOfOrdersAtLeastOnce = 0;
        $lifetimeSales = 0;
        foreach ($referrals as $referral) {
            $newCustomer = $referral->getNewCustomer();
            $lifetimeSales += $this->getSumOfOrdersByCustomer($newCustomer);
            $ordersNum = $this->getNumberOfOrdersByCustomer($newCustomer);
            $numberOfOrders += $ordersNum;
            if ($ordersNum > 0) {
                ++$numberOfOrdersAtLeastOnce;
            }
        }
        $customer->setData(self::OPTION_REFERRED_ORDERS_SUM, $lifetimeSales);
        $customer->setData(self::OPTION_REFERRED_ORDERS_NUMBER, $numberOfOrders);
        $customer->setData(self::OPTION_REFERRED_NUMBER__ORDERED_AT_LEAST_ONCE, $numberOfOrdersAtLeastOnce);

        $customer->setData(self::OPTION_IS_REFERRAL, $this->isReferral($customer));
        $customer->setData(self::OPTION_IS_REFEREE, $this->isReferee($customer));

        return $customer;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return bool
     */
    protected function isReferee($customer)
    {
        return (bool)$this->referralCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addFieldToFilter('status', [
                \Mirasvit\Rewards\Model\Config::REFERRAL_STATUS_SIGNUP,
            ])
            ->count();
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return bool
     */
    protected function isReferral($customer)
    {
        return (bool)$this->referralCollectionFactory->create()
            ->addFieldToFilter('new_customer_id', $customer->getId())
            ->addFieldToFilter('status', [
                \Mirasvit\Rewards\Model\Config::REFERRAL_STATUS_SIGNUP,
                \Mirasvit\Rewards\Model\Config::REFERRAL_STATUS_MADE_ORDER,
            ])
            ->count();
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return int
     */
    protected function getNumberOfOrdersByCustomer($customer)
    {
        $numberOfOrders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_email', $customer->getEmail());
        if ($this->getConfig()->getGeneralIsEarnAfterInvoice()) {
            $numberOfOrders->addFieldToFilter('total_invoiced', ['notnull' => 0]);
        } else {
            $numberOfOrders->addFieldToFilter('status', $this->getConfig()->getGeneralEarnInStatuses());
        }

        return $numberOfOrders->count();
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return float
     */
    protected function getSumOfOrdersByCustomer($customer)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Sale\Collection $customerTotals */
        $customerTotals = $this->saleCollectionFactory->create();
        $customerTotals
            ->addFieldToFilter('customer_email', $customer->getEmail())
            ->setOrderStateFilter(\Magento\Sales\Model\Order::STATE_CANCELED, true)
            ->addFieldToFilter('status', $this->getConfig()->getGeneralEarnInStatuses())
            ->load();
        $customerTotals = $customerTotals->getTotals();
        $lifetimeSales = floatval($customerTotals['lifetime']);

        return $lifetimeSales;
    }
}
