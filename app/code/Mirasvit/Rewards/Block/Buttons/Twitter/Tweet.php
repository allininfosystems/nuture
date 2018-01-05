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



namespace Mirasvit\Rewards\Block\Buttons\Twitter;

class Tweet extends \Mirasvit\Rewards\Block\Buttons\AbstractButtons
{
    public function __construct(
        \Mirasvit\Rewards\Helper\Balance $rewardsSocialBalance,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior,
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->rewardsSocialBalance = $rewardsSocialBalance;
        $this->rewardsBehavior      = $rewardsBehavior;
        $this->context              = $context;
        
        parent::__construct($config, $registry, $customerFactory, $customerSession, $productFactory, $context, $data);
    }

    /**
     * @return string
     */
    public function getTweetUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('rewards/twitter/tweet');
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
        $earnedTransaction = $this->rewardsSocialBalance
            ->getEarnedPointsTransaction(
                $customer, \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_TWITTER_TWEET.'-'.$url
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

        return $this->rewardsBehavior
            ->getEstimatedEarnPoints(
                \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_TWITTER_TWEET, $this->_getCustomer(), false, $url
            );
    }

    /**
     * @return bool|int
     * @deprecated rename
     */
    public function getEstimatedPointsAmount()
    {
        return $this->getEstimatedEarnPoints();
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return rtrim(parent::getCurrentUrl(), '/');
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        $name = '';
        if ($product = $this->registry->registry('current_product')) {
            $name = $product->getName();
        } elseif ($id = $this->getRequest()->getParam('id')) {
            $product = $this->productFactory->create()->load($id);
            $name = $product->getName();
        }
        return $name;
    }
}
