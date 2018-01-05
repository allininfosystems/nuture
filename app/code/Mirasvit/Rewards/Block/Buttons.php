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



namespace Mirasvit\Rewards\Block;

class Buttons extends \Mirasvit\Rewards\Block\Buttons\AbstractButtons
{
    public function __construct(
        \Magento\Framework\App\Http\Context $httpContext,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior,
        \Mirasvit\Rewards\Model\Config $rewardsConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct(
            $rewardsConfig, $registry, $customerFactory, $customerSession, $productFactory, $context, $data
        );

        $this->rewardsConfig = $rewardsConfig;
        $this->rewardsBehavior = $rewardsBehavior;
        $this->registry = $registry;
        $this->context = $context;
        $this->httpContext = $httpContext;
        $this->_isScopePrivate = true;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $layout = $this->getLayout();
        $facebook = $layout->createBlock(
            '\Mirasvit\Rewards\Block\Buttons\Facebook\Like')->setTemplate('buttons/facebook/like.phtml'
        );
        $facebookShare = $layout->createBlock(
            '\Mirasvit\Rewards\Block\Buttons\Facebook\Share')->setTemplate('buttons/facebook/share.phtml'
        );
        $twitter = $layout->createBlock(
            '\Mirasvit\Rewards\Block\Buttons\Twitter\Tweet')->setTemplate('buttons/twitter/tweet.phtml'
        );
        $pinterest = $layout->createBlock(
            '\Mirasvit\Rewards\Block\Buttons\Pinterest\Pin')->setTemplate('buttons/pinterest/pin.phtml'
        );
        $googleplus = $layout->createBlock(
            '\Mirasvit\Rewards\Block\Buttons\Googleplus\One')->setTemplate('buttons/googleplus/one.phtml'
        );
        $referral = $layout->createBlock(
            '\Mirasvit\Rewards\Block\Buttons\Referral')->setTemplate('buttons/referral.phtml'
        );

        $this->setChild('facebook.like', $facebook);
        $this->setChild('facebook.share', $facebookShare);
        $this->setChild('twitter.tweet', $twitter);
        $this->setChild('pinterest.pin', $pinterest);
        $this->setChild('googleplus.one', $googleplus);
        $this->setChild('referral', $referral);
    }

    /**
     * @return int
     */
    public function getEstimatedEarnPoints()
    {
        $url = $this->getCurrentUrl();

        return $this->rewardsBehavior->getEstimatedEarnPoints(
            \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_FACEBOOK_LIKE, $this->_getCustomer(), false, $url
        ) + $this->rewardsBehavior->getEstimatedEarnPoints(
            \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_TWITTER_TWEET, $this->_getCustomer(), false, $url
        ) + $this->rewardsBehavior->getEstimatedEarnPoints(
            \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_GOOGLEPLUS_ONE, $this->_getCustomer(), false, $url
        );
    }

    /**
     * @return bool
     */
    public function isShareActive()
    {
        return $this->getConfig()->getFacebookShowShare();
    }

    /**
     * @return bool
     */
    public function isLikeActive()
    {
        return $this->getConfig()->getFacebookIsActive();
    }

    /**
     * @return bool
     */
    public function isTweetActive()
    {
        return $this->getConfig()->getTwitterIsActive();
    }

    /**
     * @return bool
     */
    public function isPinActive()
    {
        return $this->registry->registry('current_product') && $this->getConfig()->getPinterestIsActive();
    }

    /**
     * @return bool
     */
    public function isOneActive()
    {
        return $this->getConfig()->getGoogleplusIsActive();
    }

    /**
     * @return bool
     */
    public function isReferralActive()
    {
        return $this->rewardsConfig->getReferralIsActive();
    }

    /**
     * @return bool
     */
    public function isAddthisActive()
    {
        return $this->rewardsConfig->getAddthisIsActive();
    }

    /**
     * @return string
     */
    public function getAddthisCode()
    {
        return $this->rewardsConfig->getAddthisCode();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isLikeActive() || $this->isTweetActive() || $this->isReferralActive()
            || $this->isPinActive() || $this->isOneActive() || $this->isAddthisActive();
    }

    /**
     * @return string
     */
    public function getShareUrl()
    {
        return $this->getUrl('rewards/facebook/share');
    }

    /**
     * @return string
     */
    public function getLikeUrl()
    {
        return $this->getUrl('rewards/facebook/like');
    }

    /**
     * @return string
     */
    public function getUnlikeUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('rewards/facebook/unlike');
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->getConfig()->getFacebookAppId();
    }

    /**
     * @return string
     */
    public function isAuthorized()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}
