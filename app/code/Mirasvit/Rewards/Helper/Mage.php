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



namespace Mirasvit\Rewards\Helper;

class Mage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Grid\CollectionFactory
     */
    protected $orderGridCollectionFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Grid\CollectionFactory $orderGridCollectionFactory
     * @param \Magento\Framework\App\Helper\Context                     $context
     * @param \Magento\Backend\Model\Url                                $backendUrlManager
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Grid\CollectionFactory $orderGridCollectionFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Url $backendUrlManager
    ) {
        $this->orderGridCollectionFactory = $orderGridCollectionFactory;
        $this->context = $context;
        $this->backendUrlManager = $backendUrlManager;
        parent::__construct($context);
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function getBackendCustomerUrl($customerId)
    {
        return $this->backendUrlManager->getUrl('adminhtml/customer/edit', ['id' => $customerId]);
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getBackendOrderUrl($orderId)
    {
        return $this->backendUrlManager->getUrl('adminhtml/sales_order/view', ['order_id' => $orderId]);
    }

    /**
     * @return $this
     */
    public function getOrderCollection()
    {
        $collection = $this->orderGridCollectionFactory->create()
            ->setOrder('entity_id');

        return $collection;
    }
}
