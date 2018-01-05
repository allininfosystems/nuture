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
use Mirasvit\Rewards\Model\Config as Config;

class SendFriendProduct implements ObserverInterface
{
    /**
     * @param \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior
    ) {
        $this->rewardsBehavior = $rewardsBehavior;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        $product = $observer->getEvent()->getProduct();
        $this->rewardsBehavior->processRule(Config::BEHAVIOR_TRIGGER_SEND_LINK, false, false, $product->getId());
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }
}
