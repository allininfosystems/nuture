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



namespace Mirasvit\Rewards\Model\Earning\Rule\Condition\Referred;

class Customer extends \Mirasvit\Rewards\Model\Earning\Rule\Condition\Customer
{
    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            self::OPTION_REFERRAL_IS_REFERRAL    => __('Is Referral'),
            self::OPTION_REFERRAL_GROUP_ID       => __('Referred: Group'),
            self::OPTION_REFERRAL_ORDERS_SUM     => __('Referred: Lifetime Sales'),
            self::OPTION_REFERRAL_ORDERS_NUMBER  => __('Referred: Number of Orders'),
            self::OPTION_REFERRAL_IS_SUBSCRIBER  => __('Referred: Is subscriber of newsletter'),
            self::OPTION_REFERRAL_REVIEWS_NUMBER => __('Referred: Number of reviews'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        $referral = $object->getReferredCustomer();
        if (!$referral) { //we don't check regular customers
            return true;
        }
        $this->setReferralAttributes($referral);
        $result = $this->validateCustomer($referral);

        return $result;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function setReferralAttributes($customer)
    {
        $lifetimeSales = $this->getSumOfOrdersByCustomer($customer);
        $numberOfOrders = $this->getNumberOfOrdersByCustomer($customer);

        $subscriber = $this->subscriberFactory->create()->loadByEmail($customer->getEmail());

        $reviews = $this->reviewCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId());
        $reviewsCount = $reviews->count();

        $customer->setData(self::OPTION_REFERRAL_IS_REFERRAL, $this->isReferral($customer));
        $customer->setData(self::OPTION_REFERRAL_GROUP_ID, $customer->getGroupId());
        $customer->setData(self::OPTION_REFERRAL_ORDERS_SUM, $lifetimeSales);

        $customer->setData(self::OPTION_REFERRAL_ORDERS_NUMBER, $numberOfOrders);
        $customer->setData(self::OPTION_REFERRAL_IS_SUBSCRIBER, $subscriber->getId() ? 1 : 0);
        $customer->setData(self::OPTION_REFERRAL_REVIEWS_NUMBER, $reviewsCount);

        return $customer;
    }
}
