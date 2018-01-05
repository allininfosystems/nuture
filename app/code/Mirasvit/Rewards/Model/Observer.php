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

class Observer
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\App\ResourceConnection  $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Framework\Model\Context           $context
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Model\Context $context
    ) {
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->context = $context;
    }

    /**
     * @param \Magento\Framework\Event\Observer $e
     * @return void
     */
    public function prepareCatalogProductCollection($e)
    {
        $websiteId = $this->storeManager->getWebsite()->getId();
        $groupId = $this->customerSession->getCustomerGroupId();

        $select = $e->getCollection()->getSelect();
        $select->joinLeft([
            'earning_rule_product' => $this->resource->getTableName('mst_rewards_earning_rule_product'), ],
            "e.entity_id = er_product_id AND er_website_id IN (0, $websiteId) AND er_customer_group_id = $groupId",
            []
        )->joinLeft([
            'earning_rule' => $this->resource->getTableName('mst_rewards_earning_rule'), ],
            'earning_rule.earning_rule_id  = earning_rule_product.earning_rule_id',
            ['points_amount']
        );
    }
}
