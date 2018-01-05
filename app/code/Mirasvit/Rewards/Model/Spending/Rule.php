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


namespace Mirasvit\Rewards\Model\Spending;

use \Mirasvit\Rewards\Api\Config\Rule\SpendingStyleInterface;

/**
 * @method \Mirasvit\Rewards\Model\ResourceModel\Spending\Rule\Collection getCollection()
 * @method \Mirasvit\Rewards\Model\Spending\Rule load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Rewards\Model\Spending\Rule setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Rewards\Model\Spending\Rule setIsMassStatus(bool $flag)
 * @method \Mirasvit\Rewards\Model\ResourceModel\Spending\Rule getResource()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Rule extends \Magento\SalesRule\Model\Rule
{
    const TYPE_PRODUCT = 'product';
    const TYPE_CART = 'cart';
    const TYPE_CUSTOM = 'custom';

    const CACHE_TAG = 'rewards_spending_rule';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewards_spending_rule';
    /**
     * @var string
     */
    protected $_eventPrefix = 'rewards_spending_rule';

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Rule\Condition\CombineFactory $spendingRuleConditionCombineFactory,
        Rule\Action\CollectionFactory $spendingRuleActionCollectionFactory,
        \Mirasvit\Rewards\Model\Spending\Rule\Condition\Product\CombineFactory $ruleConditionProductCombineFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\SalesRule\Model\Coupon\CodegeneratorFactory $codegenFactory,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,
        \Magento\SalesRule\Model\ResourceModel\Coupon\Collection $couponCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->spendingRuleConditionCombineFactory = $spendingRuleConditionCombineFactory;
        $this->spendingRuleActionCollectionFactory = $spendingRuleActionCollectionFactory;
        $this->ruleConditionProductCombineFactory  = $ruleConditionProductCombineFactory;
        $this->context                             = $context;
        $this->registry                            = $registry;
        $this->resource                            = $resource;
        $this->resourceCollection                  = $resourceCollection;

        parent::__construct($context, $registry, $formFactory, $localeDate, $couponFactory, $codegenFactory,
            $condCombineFactory, $condProdCombineF, $couponCollection, $storeManager, $resource, $resourceCollection,
            $data);
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Rewards\Model\ResourceModel\Spending\Rule');
        $this->setIdFieldName('spending_rule_id');
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /** Rule Methods **/
    /**
     * @return Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->spendingRuleConditionCombineFactory->create();
    }

    /**
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->ruleConditionProductCombineFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getConditions()
    {
        $condition = null;
        try {
            $condition = parent::getConditions();
        } catch (\Exception $e) {
            if ($serializeObj = $this->getSerializer()) {
                $origin = clone $this->serializer;
                $this->serializer = $serializeObj;
                $condition = parent::getConditions();
                $this->serializer = $origin;
            }
        }

        return $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        $action = null;
        try {
            $action = parent::getActions();
        } catch (\Exception $e) {
            if ($serializeObj = $this->getSerializer()) {
                $origin = clone $this->serializer;
                $this->serializer = $serializeObj;
                $action = parent::getActions();
                $this->serializer = $origin;
            }
        }

        return $action;
    }

    /**
     * @return bool|\Magento\Framework\Serialize\Serializer\Json
     */
    protected function getSerializer()
    {
        $serializer = false;
        if (class_exists(\Magento\Framework\Serialize\Serializer\Serialize::class)) {
            $serializer = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Serialize\Serializer\Serialize::class
            );
        }

        return $serializer;

    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductIds()
    {
        return $this->_getResource()->getRuleProductIds($this->getId());
    }

    /**
     * @param string $format
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toString($format = '')
    {
        $this->load($this->getId());
        $string = $this->getConditions()->asStringRecursive();

        $string = nl2br(preg_replace('/ /', '&nbsp;', $string));

        return $string;
    }
    /************************/

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function applyAll()
    {
        $this->_getResource()->applyAllRulesForDateRange();
    }

    /**
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->getData('website_ids');
    }

    /**
     * @return array
     */
    public function getSpendingStyle()
    {
        return $this->getData('spending_style') ?: SpendingStyleInterface::STYLE_PARTIAL;
    }

    /**
     * @return bool|float
     */
    public function getSpendMinPointsNumber()
    {
        $min = parent::getSpendMinPoints();
        if (strpos($min, '%') === false) {
            return $min;
        }

        return false;
    }

    /**
     * @param float $subtotal
     * @return bool|float
     */
    public function getSpendMinAmount($subtotal)
    {
        $min = parent::getSpendMinPoints();
        if (strpos($min, '%') === false) {
            return $this->getSpendMinPointsNumber();
        }
        $min = str_replace('%', '', $min);

        return ceil($subtotal * $min / 100 / $this->getMonetaryStep($subtotal) * $this->getSpendPoints());
    }

    /**
     * @return bool|float
     */
    public function getSpendMaxPointsNumber()
    {
        $max = parent::getSpendMaxPoints();
        if (strpos($max, '%') === false) {
            return $max;
        }

        return false;
    }

    /**
     * @param float $subtotal
     * @return bool|float
     */
    public function getSpendMaxAmount($subtotal)
    {
        $max = parent::getSpendMaxPoints() ?: '100%';
        if (strpos($max, '%') === false) {
            return $this->getSpendMaxPointsNumber();
        }
        $max = str_replace('%', '', $max);

        $points = $subtotal * $max / 100 / $this->getMonetaryStep($subtotal) * $this->getSpendPoints();

        return $max == 100 ? ceil($points) : floor($points);//if points is limited we should not exceed it
    }

    /**
     * @param float $subtotal
     * @return bool|float
     */
    public function getMonetaryStep($subtotal = 0)
    {
        $value = parent::getMonetaryStep();
        if (!$subtotal || strpos($value, '%') === false) {
            return str_replace('%', '', $value);
        }
        $value = str_replace('%', '', $value);

        return $subtotal * $value / 100;
    }

    /**
     * {@inheritdoc}
     */
    public function validateFields()
    {
        if ($this->getSpendMaxPoints() && $this->getSpendMinPoints() >= $this->getSpendMaxPoints()
        ) {
            return false;
        }

        return true;
    }
}
