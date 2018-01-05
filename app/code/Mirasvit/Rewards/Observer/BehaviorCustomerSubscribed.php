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
use Magento\Newsletter\Model\Subscriber;
use Mirasvit\Rewards\Model\Config;

class BehaviorCustomerSubscribed implements ObserverInterface
{
    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    /**
     * @param \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior,
        \Magento\Framework\Registry $registry
    ) {
        $this->rewardsBehavior = $rewardsBehavior;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        /** @var Subscriber $subscriber */
        $subscriber = $observer->getEvent()->getDataObject();

        if ($subscriber->getStatus() == Subscriber::STATUS_SUBSCRIBED) {
            //            $s = $this->registry->registry('subscriber_status');
            //            if ($s == null || $s !== Subscriber::STATUS_SUBSCRIBED) {
                //todo fix issue: we give points even for unsubscription. it's magento incorrect event issue.
                $this->rewardsBehavior->processRule(Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP);
            //            } else {
            //                $this->registry->register('subscriber_status', Subscriber::STATUS_SUBSCRIBED);
            //            }
        }
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }
}
