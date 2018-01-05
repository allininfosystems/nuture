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
use Magento\Store\Model\StoreFactory;
use Mirasvit\Rewards\Model\Config;
use Mirasvit\Rewards\Helper\Behavior;

class PushNotificationSubscriberSaveAfter implements ObserverInterface
{
    public function __construct(
        Behavior $behaviorHelper,
        Config $config,
        StoreFactory $storeFactory
    ) {
        $this->behaviorHelper = $behaviorHelper;
        $this->config         = $config;
        $this->storeFactory   = $storeFactory;
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
        /** @var \Mirasvit\PushNotification\Api\Data\SubscriberInterface $subscriber */
        $subscriber = $observer->getEvent()->getEntity();
        $entityType = $observer->getEvent()->getEntityType();

        if ($entityType != 'Mirasvit\PushNotification\Api\Data\SubscriberInterface') {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }

        $websiteId = $this->storeFactory->create()->load($subscriber->getStoreId())->getWebsiteId();
        $this->behaviorHelper->processRule(
            Config::BEHAVIOR_TRIGGER_PUSHNOTIFICATION_SIGNUP,
            $subscriber->getCustomerId(),
            $websiteId
        );
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }
}
