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

use Mirasvit\Rewards\Model\Config as Config;
use Mirasvit\Rewards\Model\Earning\Rule\Condition as EarningRuleCondition;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    public function __construct(
        ProductFactory $earningRuleConditionProductFactory,
        CustomerFactory $earningRuleConditionCustomerFactory,
        CartFactory $earningRuleConditionCartFactory,
        Referred\CustomerFactory $earningRuleConditionReferredCustomerFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\SalesRule\Model\Rule\Condition\AddressFactory $ruleConditionAddressFactory,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->earningRuleConditionProductFactory          = $earningRuleConditionProductFactory;
        $this->earningRuleConditionCustomerFactory         = $earningRuleConditionCustomerFactory;
        $this->earningRuleConditionCartFactory             = $earningRuleConditionCartFactory;
        $this->earningRuleConditionReferredCustomerFactory = $earningRuleConditionReferredCustomerFactory;
        $this->request                                     = $request;
        $this->ruleConditionAddressFactory                 = $ruleConditionAddressFactory;
        $this->eventManager                                = $eventManager;

        $this->setType('\\Mirasvit\\Rewards\\Model\\Earning\\Rule\\Condition\\Combine');
    }

    /**
     * @return array
     */
    private function getReferralEvents()
    {
        return [
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER,
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP,
        ];
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        if ($this->getRule()->getType()) {
            $type = $this->getRule()->getType();
        } else {
            $type = $this->request->getParam('rule_type');
        }

        if ($type == \Mirasvit\Rewards\Model\Earning\Rule::TYPE_CART) {
            return $this->_getCartConditions();
        } elseif ($type == \Mirasvit\Rewards\Model\Earning\Rule::TYPE_BEHAVIOR) {
            $itemAttributes = $this->_getCustomerAttributes();
            $attributes = $this->convertToAttributes($itemAttributes, 'customer', 'Customer');
            if (in_array($this->getRule()->getBehaviorTrigger(), $this->getReferralEvents())) {
                $itemAttributes = $this->_getReferredCustomerAttributes();
                $attributesReffered = $this->convertToAttributes(
                    $itemAttributes,
                    'Referred\Customer',
                    'Referred Customer'
                );
                $attributes = array_merge_recursive($attributes, $attributesReffered);
            }
        } else {
            $itemAttributes = $this->_getProductAttributes();
            $attributes = $this->convertToAttributes($itemAttributes, 'product', 'Product Attributes');
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => '\\Mirasvit\\Rewards\\Model\\Earning\\Rule\\Condition\\Combine',
                'label' => __('Conditions Combination'),
            ],
        ]);

        foreach ($attributes as $group => $arrAttributes) {
            $conditions = array_merge_recursive($conditions, [
                [
                    'label' => $group,
                    'value' => $arrAttributes,
                ],
            ]);
        }

        return $conditions;
    }

    /**
     * @param array  $itemAttributes
     * @param string $condition
     * @param string $group
     * @return array
     */
    protected function convertToAttributes($itemAttributes, $condition, $group)
    {
        $attributes = [];
        foreach ($itemAttributes as $code => $label) {
            $attributes[$group][] = [
                'value' => '\\Mirasvit\\Rewards\\Model\\Earning\\Rule\\Condition\\'.ucfirst($condition).'|'.$code,
                'label' => $label,
            ];
        }

        return $attributes;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function _getProductAttributes()
    {
        $productCondition = $this->earningRuleConditionProductFactory->create();
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        return $productAttributes;
    }

    /**
     * @return array
     */
    protected function _getCartConditions()
    {
        $attributes = [];

        $addressCondition  = $this->ruleConditionAddressFactory->create();
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Mirasvit\Rewards\Model\Earning\Rule\Condition\Address|'.$code,
                'label' => $label
            ];
        }

        $cartCondition  = $this->earningRuleConditionCartFactory->create();
        $cartAttributes = $cartCondition->loadAttributeOptions()->getAttributeOption();
        foreach ($cartAttributes as $code => $label) {
            $attributes[] = ['value' => 'Mirasvit\Rewards\Model\Earning\Rule\Condition\Cart|'.$code, 'label' => $label];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Found',
                'label' => __('Product attribute combination')
            ],
            [
                'value' => 'Magento\SalesRule\Model\Rule\Condition\Product\Subselect',
                'label' => __('Products subselection')
            ],
            [
                'value' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
                'label' => __('Conditions combination')
            ],
            [
                'label' => __('Cart Attribute'),
                'value' => $attributes
            ],
        ]);

        $additional = new \Magento\Framework\DataObject();
        $this->eventManager->dispatch('salesrule_rule_condition_combine', ['additional' => $additional]);
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

    /**
     * @return array
     */
    protected function _getCustomerAttributes()
    {
        $customerCondition = $this->earningRuleConditionCustomerFactory->create();
        $customerAttributes = $customerCondition->loadAttributeOptions()->getAttributeOption();

        return $customerAttributes;
    }

    /**
     * @return array
     */
    protected function _getReferredCustomerAttributes()
    {
        $customerCondition = $this->earningRuleConditionReferredCustomerFactory->create();
        $customerAttributes = $customerCondition->loadAttributeOptions()->getAttributeOption();

        return $customerAttributes;
    }
}
