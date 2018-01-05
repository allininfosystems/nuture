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



namespace Mirasvit\Rewards\Helper\Output;

class Earn
{
    public function __construct(
        \Mirasvit\Rewards\Model\Config $config,
        \Mirasvit\Rewards\Helper\Balance\Earn $earnHelper,
        \Mirasvit\Rewards\Helper\Balance\Spend $spendHelper,
        \Mirasvit\Rewards\Helper\Data $rewardsDataHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->config            = $config;
        $this->earnHelper        = $earnHelper;
        $this->spendHelper       = $spendHelper;
        $this->rewardsDataHelper = $rewardsDataHelper;
        $this->registry          = $registry;
        $this->customerSession   = $customerSession;
        $this->storeManager   = $storeManager;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function getProductPoints(\Magento\Catalog\Model\Product $product){
        $n = $this->earnHelper->getRoundingProductPoints(
            $product,
            $this->customerSession->getCustomerGroupId(),
            $this->storeManager->getStore()->getWebsiteId(),
            $this->earnHelper->getProductPriceByProduct($product)
        );
        return $n;
    }

}