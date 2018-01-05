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



namespace Mirasvit\Rewards\Helper\Rule;

use Mirasvit\Rewards\Model\Config as Config;
use Mirasvit\Rewards\Model\ResourceModel\Notification\Rule\CollectionFactory;

class Notification extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var CollectionFactory
     */
    protected $notificationRuleCollectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $cartFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param CollectionFactory                     $notificationRuleCollectionFactory
     * @param \Magento\Checkout\Model\CartFactory   $cartFactory
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        CollectionFactory $notificationRuleCollectionFactory,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->notificationRuleCollectionFactory = $notificationRuleCollectionFactory;
        $this->cartFactory = $cartFactory;
        $this->context = $context;
        $this->request = $context->getRequest();
        parent::__construct($context);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return array
     */
    public function calcNotificationRules()
    {
        return $this->calcRules();
    }

    /**
     * @return array
     */
    public function calcNotificationRulesWidget()
    {
        return $this->calcRules(true);
    }

    /**
     * @return bool|string
     */
    protected function getType()
    {
        $request = $this->request;
        $action = $request->getModuleName().'_'.$request->getControllerName().'_'.$request->getActionName();

        $type = false;
        switch ($action) {
            case 'rewards_account_index':
                $type = Config::NOTIFICATION_POSITION_ACCOUNT_REWARDS;
                break;
            case 'rewards_referral_index':
                $type = Config::NOTIFICATION_POSITION_ACCOUNT_REFERRALS;
                break;
            case 'checkout_cart_index':
                $type = Config::NOTIFICATION_POSITION_CART;
                break;
            case 'checkout_onepage_index':
            case 'onestepcheckout_index_index':
            case 'checkout_index_index':
                $type = Config::NOTIFICATION_POSITION_CHECKOUT;
                break;
        }

        return $type;
    }

    /**
     * @param bool|false $isWidget
     * @return array
     */
    protected function calcRules($isWidget = false)
    {
        $quote           = $this->cartFactory->create()->getQuote();
        $websiteId       = $quote->getStore()->getWebsiteId();
        $customerGroupId = $quote->getCustomerGroupId();

        $rulesCollection = $this->notificationRuleCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addCustomerGroupFilter($customerGroupId)
            ->addCurrentFilter()
            ->setOrder('sort_order')
        ;

        if (!$isWidget) {
            $rulesCollection->addTypeFiler($this->getType());
        }

        $rules = [];
        $quote->collectTotals();
        foreach ($rulesCollection as $rule) {
            $rule->afterLoad();
            if ($quote->getItemVirtualQty() > 0) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
            // https://github.com/magento/magento2/issues/3853
            if (!(float)$address->getTotalQty()) {
                $address->setTotalQty($quote->getItemsQty());
            }
            if ($rule->validate($address)) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function calcNotificationMessages()
    {
        $messages = [];
        $rules = $this->calcNotificationRules();
        foreach ($rules as $rule) {
            $messages[] = $rule->getMessage();
        }

        return $messages;
    }
}
