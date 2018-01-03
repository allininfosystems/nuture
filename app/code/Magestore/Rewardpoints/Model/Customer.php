<?php
namespace Magestore\Rewardpoints\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Customer
 * @package Magestore\Rewardpoints\Model
 */
class Customer extends \Magento\Framework\Model\AbstractModel implements \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface
{
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        $this->_customer = $customerFactory;
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Rewardpoints\Model\ResourceModel\Customer');
    }

    /**
     * @return mixed
     */
    public function getRewardId()
    {
        return $this->getData(self::REWARD_ID);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function setRewardId($id)
    {
        return $this->setData(self::REWARD_ID, $id);
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function setCustomerId($id)
    {
        return $this->setData(self::CUSTOMER_ID, $id);
    }

    /**
     *
     */
    public function getPointBalance()
    {
        return $this->getData(self::POINT_BALANCE);
    }

    /**
     * @param $balance
     */
    public function setPointBalance($balance)
    {
        return $this->setData(self::POINT_BALANCE, $balance);
    }

    /**
     *
     */
    public function getHoldingBalance()
    {
        return $this->getData(self::HOLDING_BALANCE);
    }

    /**
     * @param $balance
     */
    public function setHoldingBalance($balance)
    {
        return $this->setData(self::HOLDING_BALANCE, $balance);
    }

    /**
     *
     */
    public function getSpentBalance()
    {
        return $this->getData(self::SPENT_BALANCE);
    }

    /**
     * @param $balance
     */
    public function setSpentBalance($balance)
    {
        return $this->setData(self::SPENT_BALANCE, $balance);
    }

    /**
     *
     */
    public function getIsNotification()
    {
        return $this->getData(self::IS_NOTIFICATION);
    }

    /**
     * @param $isNotification
     */
    public function setIsNotification($isNotification)
    {
        return $this->setData(self::IS_NOTIFICATION, $isNotification);
    }

    /**
     *
     */
    public function getExpireNotification()
    {
        return $this->getData(self::EXPIRE_NOTIFICATION);
    }

    /**
     * @param $expireNotification
     */
    public function setExpireNotification($expireNotification)
    {
        return $this->setData(self::EXPIRE_NOTIFICATION, $expireNotification);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        $email = $this->getData('email');
        if(!$email){
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->_customer->create();
            $customer->_getResource()->load($customer,$this->getCustomerId());
            $email = $customer->getEmail();
        }
        return $email;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function setEmail($email)
    {
        return $this->setData('email', $email);
    }


}