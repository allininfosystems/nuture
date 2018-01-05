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



namespace Mirasvit\Rewards\Helper;

class Message extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Email\Model\Template\Filter $templateFilter,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->templateFilter  = $templateFilter;
        $this->customerSession = $customerSession;
        $this->rewardsData     = $rewardsData;
        $this->rewardsBalance  = $rewardsBalance;
        $this->context         = $context;

        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getNoteWithVariables()
    {
        $note = __('You can use the following variables:').'<br>';
        $note .= '{{var customer.name}} - customer name<br>';
        $note .= '{{store url=""}} - store URL<br>';
        $note .= '{{var store.getFrontendName()}} - store name<br>';
        $note .= '{{var transaction_amount}} - formatted amount of current transaction (e.g 10 Rewards Points)<br>';
        $note .= '{{var balance_total}} - formatted balance of customer account (e.g. 100 Rewards Points)<br>';
        $note .= 'Leave empty to use default notification email. <br>';

        return $note;
    }

    /**
     * @return string
     */
    public function getNotificationNoteWithVariables()
    {
        $note = __('You can use the following variables:').'<br>';
        $note .= '{{var customer.name}} - customer name<br>';
        $note .= '{{store url=""}} - store URL<br>';
        $note .= '{{var store.getFrontendName()}} - store name<br>';
        $note .= '{{var balance_total}} - formatted balance of customer account (e.g. 100 Rewards Points)<br>';

        return $note;
    }

    /**
     * @return string
     */
    public function getEarningRuleNotificationNote()
    {
        $note = __('You can use the following variables:').'<br>';
        $note .= '{{var customer.name}} - customer name<br>';
        $note .= '{{store url=""}} - store URL<br>';
        $note .= '{{var store.getFrontendName()}} - store name<br>';
        $note .= '{{var balance_total}} - formatted balance of customer account (e.g. 100 Rewards Points)<br>';

        return $note;
    }

    /**
     * @param string $message
     *
     * @return string
     */
    public function processNotificationVariables($message)
    {
        $customer = $this->getCustomer();
        $this->templateFilter->setVariables([
            'customer'      => $customer,
            'store'         => $customer->getStore(),
            'balance_total' => $this->rewardsData->formatPoints($this->rewardsBalance->getBalancePoints($customer)),
        ]);

        return $this->templateFilter->filter($message);
    }

    /**
     * @param array $message
     *
     * @return string
     */
    public function processProductNotificationVariables($message)
    {
        $customer = $this->getCustomer();
        $this->templateFilter->setVariables([
            'customer'      => $customer,
            'store'         => $customer->getStore(),
            'balance_total' => $this->rewardsData->formatPoints($this->rewardsBalance->getBalancePoints($customer)),
        ]);

        return $this->templateFilter->filter($message);
    }

    /**
     * Get logged in customer.
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }
}
