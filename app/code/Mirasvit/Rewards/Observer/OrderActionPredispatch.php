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

class OrderActionPredispatch extends Order
{
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
        $uri = $observer->getControllerAction()->getRequest()->getRequestUri();
        if (strpos($uri, 'checkout') === false) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        if (!($quote = $this->cartFactory->create()->getQuote()) || !$quote->getId()) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        //this does not calculate quote correctly
        if (strpos($uri, '/checkout/cart/add/') !== false) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        if (strpos($uri, '/checkout/sidebar/removeItem/') !== false) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        if (strpos($uri, '/checkout/sidebar/updateItemQty') !== false) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }

        //this does not calculate quote correctly with firecheckout
        if (strpos($uri, '/firecheckout/') !== false) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }

        //this does not calculate quote correctly with gomage
        if (strpos($uri, '/gomage_checkout/onepage/save/') !== false) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        $this->refreshPoints($quote);
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }
}
