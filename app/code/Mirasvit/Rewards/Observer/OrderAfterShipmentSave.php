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



namespace Mirasvit\Rewards\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Rewards\Model\Config;

class OrderAfterShipmentSave implements ObserverInterface
{
    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Order
     */
    protected $rewardsBalanceOrder;

    /**
     * @var \Mirasvit\Rewards\Helper\Referral
     */
    protected $rewardsReferral;

    public function __construct(
        \Mirasvit\Rewards\Helper\Behavior $behaviorHelper,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\State $appState
    ) {
        $this->behaviorHelper = $behaviorHelper;
        $this->config         = $config;
        $this->storeFactory   = $storeFactory;
        $this->objectManager  = $objectManager;
        $this->orderFactory   = $orderFactory;
        $this->appState       = $appState;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        $object = $observer->getObject();
        if (!($object && ($object instanceof \Magento\Sales\Model\Order\Shipment))) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }

        /**
         * To prevent error during installation(Helpers are using session).
         */
        try {
            $this->appState->getAreaCode();
        }
        catch (\Exception $e) {
            return;
        }

        $this->rewardsBalanceOrder = $this->objectManager->get('\Mirasvit\Rewards\Helper\Balance\Order');
        $this->rewardsReferral = $this->objectManager->get('\Mirasvit\Rewards\Helper\Referral');

        $order = $this->orderFactory->create()->load((int) $object->getOrderId());

        if ($order && $this->config->getGeneralIsEarnAfterShipment()) {
            $this->earnOrderPoints($order);
        }
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return void
     */
    protected function earnOrderPoints($order)
    {
        if ($order->getCustomerId()) {
            $this->rewardsBalanceOrder->earnOrderPoints($order);

            $websiteId = $this->storeFactory->create()->load($order->getStoreId())->getWebsiteId();
            $this->behaviorHelper->processRule(
                Config::BEHAVIOR_TRIGGER_CUSTOMER_ORDER,
                $order->getCustomerId(),
                $websiteId,
                $order->getId(),
                ['order' => $order]
            );
        }

        $this->rewardsReferral->processReferralOrder($order);
    }
}
