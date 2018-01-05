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



namespace Mirasvit\Rewards\Block\Buttons\Pinterest;

class Pin extends \Mirasvit\Rewards\Block\Buttons\AbstractButtons
{
    public function __construct(
        \Mirasvit\Rewards\Helper\Balance $rewardsSocialBalance,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior,
        \Magento\Catalog\Helper\Image $catalogImage,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        $this->rewardsSocialBalance = $rewardsSocialBalance;
        $this->rewardsBehavior      = $rewardsBehavior;
        $this->catalogImage         = $catalogImage;
        $this->registry             = $registry;
        $this->context              = $context;

        parent::__construct($config, $registry, $customerFactory, $customerSession, $productFactory, $context);
    }

    /**
     * @return string
     */
    public function getPinUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('rewards/pinterest/pin');
    }

    /**
     * @return bool
     */
    public function isLiked()
    {
        if (!$customer = $this->_getCustomer()) {
            return false;
        }
        $url = $this->getCurrentUrl();
        $earnedTransaction = $this->rewardsSocialBalance->getEarnedPointsTransaction(
            $customer, \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_PINTEREST_PIN.'-'.$url
        );
        if ($earnedTransaction) {
            return true;
        }
    }

    /**
     * @return bool|int
     */
    public function getEstimatedEarnPoints()
    {
        $url = $this->getCurrentUrl();

        return $this->rewardsBehavior->getEstimatedEarnPoints(
            \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_PINTEREST_PIN, $this->_getCustomer(), false, $url
        );
    }

    /**
     * @return bool|string
     */
    public function getMediaUrl()
    {
        if (!$product = $this->registry->registry('current_product')) {
            return false;
        }

        return $this->catalogImage->init($product, 'product_base_image')->getUrl();
    }

    /**
     * @return bool|\Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$product = $this->registry->registry('current_product')) {
            return false;
        }

        return $product;
    }
    /**
     * @return int
     * @deprecated
     */
    public function getEstimatedPointsAmount()
    {
        return $this->getEstimatedEarnPoints();
    }
}
