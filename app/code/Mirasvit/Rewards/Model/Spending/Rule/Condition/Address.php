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


namespace Mirasvit\Rewards\Model\Spending\Rule\Condition;

class Address extends \Magento\SalesRule\Model\Rule\Condition\Address
{
    const OPTION_COUPON_USED = 'coupon_used';
    const OPTION_COUPON_CODE = 'coupon_code';
    const OPTION_DISCOUNT_USED = 'discount_code';

    public function __construct(
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods,
        \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods,
        array $data = []
    ) {
        parent::__construct(
            $context, $directoryCountry, $directoryAllregion, $shippingAllmethods, $paymentAllmethods, $data
        );

        $this->taxConfig = $taxConfig;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        parent::loadAttributeOptions();
        $attributes = $this->getAttributeOption();
        $attributes[self::OPTION_COUPON_USED]   = __('Coupon Used');
        $attributes[self::OPTION_COUPON_CODE]   = __('Coupon Code');
        $attributes[self::OPTION_DISCOUNT_USED] = __('Discount Used');

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case self::OPTION_COUPON_USED:
            case self::OPTION_DISCOUNT_USED:
                return 'select';
        }
        return parent::getInputType();
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case self::OPTION_COUPON_USED:
            case self::OPTION_DISCOUNT_USED:
                return 'select';
        }
        return parent::getValueElementType();
    }

    /**
     * {@inheritdoc}
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case self::OPTION_COUPON_USED:
                case self::OPTION_DISCOUNT_USED:
                    $options = [
                        ['value' => 0, 'label' => __('No')],
                        ['value' => 1, 'label' => __('Yes')],
                    ];
                    break;
                default:
                    $options = [];
            }
            if (!$options) {
                $options = parent::getValueSelectOptions();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $address = $model;
        if (!$address instanceof \Magento\Quote\Model\Quote\Address) {
            if ($model->getQuote()->isVirtual()) {
                $address = $model->getQuote()->getBillingAddress();
            } else {
                $address = $model->getQuote()->getShippingAddress();
            }
        }

        if ('payment_method' == $this->getAttribute() && !$address->hasPaymentMethod()) {
            $address->setPaymentMethod($model->getQuote()->getPayment()->getMethod());
        }

        if (
            'base_subtotal' == $this->getAttribute() &&
            $this->taxConfig->displayCartSubtotalInclTax($address->getQuote()->getStore())
        ) {
            $address->setData($this->getAttribute(), $address->getSubtotalInclTax());
        }

        $address->setData(self::OPTION_COUPON_USED, (int)!empty($address->getQuote()->getCouponCode()));
        $address->setData(self::OPTION_COUPON_CODE, $address->getQuote()->getCouponCode());
        $address->setData(self::OPTION_DISCOUNT_USED, (int)!empty($address->getQuote()->getAppliedRuleIds()));

        return parent::validate($address);
    }
}
