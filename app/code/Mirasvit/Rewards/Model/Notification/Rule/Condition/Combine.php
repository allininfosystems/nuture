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



namespace Mirasvit\Rewards\Model\Notification\Rule\Condition;

use \Mirasvit\Rewards\Model as Model;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Mirasvit\Rewards\Model\Notification\Rule\Condition\ProductFactory
     */
    protected $notificationRuleConditionProductFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\AddressFactory
     */
    protected $ruleConditionAddressFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Notification\Rule\Condition\CustomFactory
     */
    protected $notificationRuleConditionCustomFactory;

    /**
     * @var \Mirasvit\Rewards\Helper\Rule
     */
    protected $rewardsRule;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param Model\Notification\Rule\Condition\ProductFactory       $notificationRuleConditionProductFactory
     * @param \Magento\SalesRule\Model\Rule\Condition\AddressFactory $ruleConditionAddressFactory
     * @param \Magento\Framework\App\Action\Context                  $context
     * @param \Magento\Rule\Model\Condition\Context                  $ruleContext
     * @param array                                                  $data
     */
    public function __construct(
        Model\Notification\Rule\Condition\ProductFactory $notificationRuleConditionProductFactory,
        \Magento\SalesRule\Model\Rule\Condition\AddressFactory $ruleConditionAddressFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Rule\Model\Condition\Context $ruleContext,
        array $data = []
    ) {
        parent::__construct($ruleContext, $data);

        $this->notificationRuleConditionProductFactory = $notificationRuleConditionProductFactory;
        $this->ruleConditionAddressFactory = $ruleConditionAddressFactory;
        $this->context = $context;

        $this->setType('\\Mirasvit\\Rewards\\Model\\Notification\\Rule\\Condition\\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $type = \Mirasvit\Rewards\Model\Notification\Rule::TYPE_CART;

        if ($type == \Mirasvit\Rewards\Model\Notification\Rule::TYPE_CUSTOM) {
            $itemAttributes = $this->_getCustomAttributes();
            $condition = 'custom';
        } elseif ($type == \Mirasvit\Rewards\Model\Notification\Rule::TYPE_CART) {
            return $this->_getCartConditions();
        } else {
            $itemAttributes = $this->_getProductAttributes();
            $condition = 'product';
        }

        $attributes = [];
        foreach ($itemAttributes as $code => $label) {
            $group = $this->rewardsRule->getAttributeGroup($code);
            $attributes[$group][] = [
                'value' => '\\Mirasvit\\Rewards\\Model\\Notification\\Rule\\Condition\\'.ucfirst($condition).'|'.$code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => '\\Mirasvit\\Rewards\\Model\\Notification\\Rule\\Condition\\Combine',
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
        $productCondition = $this->notificationRuleConditionProductFactory->create();
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        return $productAttributes;
    }

    /**
     * @return array
     */
    protected function _getCartConditions()
    {
        $addressCondition = $this->ruleConditionAddressFactory->create();
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = ['value' => '\Magento\SalesRule\Model\Rule\Condition\Address|'.$code, 'label' => $label];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => '\Magento\SalesRule\Model\Rule\Condition\Product\Found',
                'label' => __('Product attribute combination')
            ],
            [
                'value' => '\Magento\SalesRule\Model\Rule\Condition\Product\Subselect',
                'label' => __('Products subselection')
            ],
            ['value' => '\Magento\SalesRule\Model\Rule\Condition\Combine', 'label' => __('Conditions combination')],
            ['label' => __('Cart Attribute'), 'value' => $attributes],
        ]);

        $additional = new \Magento\Framework\DataObject();
        $this->context->getEventManager()->dispatch('salesrule_rule_condition_combine', ['additional' => $additional]);
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }

    /**
     * @return array
     */
    protected function _getCustomAttributes()
    {
        $customCondition = $this->notificationRuleConditionCustomFactory->create();
        $customAttributes = $customCondition->loadAttributeOptions()->getAttributeOption();

        return $customAttributes;
    }
}
