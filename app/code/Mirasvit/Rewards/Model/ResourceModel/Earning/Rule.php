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



namespace Mirasvit\Rewards\Model\ResourceModel\Earning;

class Rule extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory
     */
    protected $earningRuleCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $resourcePrefix;

    /**
     * @param \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory $earningRuleCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory       $productCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\Context                    $context
     * @param string                                                               $resourcePrefix
     */
    public function __construct(
        \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\CollectionFactory $earningRuleCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        $this->earningRuleCollectionFactory = $earningRuleCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mst_rewards_earning_rule', 'earning_rule_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\AbstractModel|\Mirasvit\Rewards\Model\Earning\Rule
     */
    protected function loadWebsiteIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Mirasvit\Rewards\Model\Earning\Rule $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_rewards_earning_rule_website'))
            ->where('earning_rule_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['website_id'];
            }
            $object->setData('website_ids', $array);
        }

        return $object;
    }

    /**
     * @param \Mirasvit\Rewards\Model\Earning\Rule $object
     * @return void
     */
    protected function saveWebsiteIds($object)
    {
        /* @var  \Mirasvit\Rewards\Model\Earning\Rule $object */
        $condition = $this->getConnection()->quoteInto('earning_rule_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_rewards_earning_rule_website'), $condition);
        foreach ((array) $object->getData('website_ids') as $id) {
            $objArray = [
                'earning_rule_id' => $object->getId(),
                'website_id' => $id,
            ];
            $this->getConnection()->insert($this->getTable('mst_rewards_earning_rule_website'), $objArray);
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\AbstractModel|\Mirasvit\Rewards\Model\Earning\Rule
     */
    protected function loadCustomerGroupIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Mirasvit\Rewards\Model\Earning\Rule $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_rewards_earning_rule_customer_group'))
            ->where('earning_rule_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['customer_group_id'];
            }
            $object->setData('customer_group_ids', $array);
        }

        return $object;
    }

    /**
     * @param \Mirasvit\Rewards\Model\Earning\Rule $object
     * @return void
     */
    protected function saveCustomerGroupIds($object)
    {
        if (is_string($object->getData('customer_group_ids'))) {
            $object->setData('customer_group_ids', explode(',', $object->getData('customer_group_ids')));
        }
        $condition = $this->getConnection()->quoteInto('earning_rule_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_rewards_earning_rule_customer_group'), $condition);
        foreach ((array) $object->getData('customer_group_ids') as $id) {
            $objArray = [
                'earning_rule_id' => $object->getId(),
                'customer_group_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_rewards_earning_rule_customer_group'), $objArray);
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Rewards\Model\Earning\Rule $object */
        if (!$object->getIsMassDelete()) {
            $this->loadWebsiteIds($object);
            $this->loadCustomerGroupIds($object);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Rewards\Model\Earning\Rule $object */
        if (!$object->getIsMassStatus()) {
            $this->saveWebsiteIds($object);
            $this->saveCustomerGroupIds($object);
        }

        return parent::_afterSave($object);
    }

    /************************/

    /**
     * @return void
     */
    public function applyAllRulesForDateRange()
    {
        $collection = $this->earningRuleCollectionFactory->create()
            ->addFieldToFilter('type', \Mirasvit\Rewards\Model\Earning\Rule::TYPE_PRODUCT)
        ;
        foreach ($collection as $rule) {
            $conds = [
                $this->getConnection()->quoteInto('earning_rule_id=?', $rule->getId()),
            ];
            $this->getConnection()->delete($this->getTable('mst_rewards_earning_rule_product'), $conds);
        }

        $products = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', ['eq' => 1])
        ;
        foreach ($products as $product) {
            $rules = $this->earningRuleCollectionFactory->create()
                ->addFieldToFilter('type', \Mirasvit\Rewards\Model\Earning\Rule::TYPE_PRODUCT)
                ->addFieldToFilter('is_active', true)
            ;

            foreach ($rules as $rule) {
                $rule->afterLoad();
                if (!$rule->validate($product)) {
                    continue;
                }
                $this->loadWebsiteIds($rule);
                $this->loadCustomerGroupIds($rule);

                foreach ($rule->getWebsiteIds() as $websiteId) {
                    foreach ($rule->getCustomerGroupIds() as $groupId) {
                        $objArray = [
                            'earning_rule_id' => $rule->getId(),
                            'er_website_id' => $websiteId,
                            'er_customer_group_id' => $groupId,
                            'er_product_id' => $product->getId(),
                        ];
                        $this->getConnection()->insert($this->getTable('mst_rewards_earning_rule_product'), $objArray);
                    }
                }
            }
        }
    }
}
