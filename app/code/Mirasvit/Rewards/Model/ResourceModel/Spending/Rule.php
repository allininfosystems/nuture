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



namespace Mirasvit\Rewards\Model\ResourceModel\Spending;

class Rule extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $resourcePrefix;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string                                            $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mst_rewards_spending_rule', 'spending_rule_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Mirasvit\Rewards\Model\Spending\Rule
     */
    protected function loadWebsiteIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Mirasvit\Rewards\Model\Spending\Rule $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_rewards_spending_rule_website'))
            ->where('spending_rule_id = ?', $object->getId());
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
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     */
    protected function saveWebsiteIds($object)
    {
        /* @var  \Mirasvit\Rewards\Model\Spending\Rule $object */
        $condition = $this->getConnection()->quoteInto('spending_rule_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_rewards_spending_rule_website'), $condition);
        foreach ((array) $object->getData('website_ids') as $id) {
            $objArray = [
                'spending_rule_id' => $object->getId(),
                'website_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_rewards_spending_rule_website'), $objArray);
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Mirasvit\Rewards\Model\Spending\Rule
     */
    protected function loadCustomerGroupIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Mirasvit\Rewards\Model\Spending\Rule $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_rewards_spending_rule_customer_group'))
            ->where('spending_rule_id = ?', $object->getId());
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
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     */
    protected function saveCustomerGroupIds($object)
    {
        /* @var  \Mirasvit\Rewards\Model\Spending\Rule $object */
        $condition = $this->getConnection()->quoteInto('spending_rule_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_rewards_spending_rule_customer_group'), $condition);
        foreach ((array) $object->getData('customer_group_ids') as $id) {
            $objArray = [
                'spending_rule_id' => $object->getId(),
                'customer_group_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_rewards_spending_rule_customer_group'), $objArray);
        }
    }

    /**
     * Retrieve customer group ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getCustomerGroupIds($ruleId)
    {
        try {
            $groupIds = parent::getCustomerGroupIds($ruleId);
        } catch (\Exception $e) {
            $groupIds = [];
        }

        return $groupIds;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Mirasvit\Rewards\Model\Spending\Rule $object */
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
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Rewards\Model\Spending\Rule $object */
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Rewards\Model\Spending\Rule $object */
        if (!$object->getIsMassStatus()) {
            $this->saveWebsiteIds($object);
            $this->saveCustomerGroupIds($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * Get all existing rule labels
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreLabels($ruleId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('salesrule_label'),
            ['store_id', 'label']
        )->where(
            'rule_id = :rule_id'
        );
        return $this->getConnection()->fetchPairs($select, [':rule_id' => $ruleId]);
    }

    /************************/
}
