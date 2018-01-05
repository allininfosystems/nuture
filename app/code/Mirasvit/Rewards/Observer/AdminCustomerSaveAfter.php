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
use Magento\Framework\Message\ManagerInterface;

class AdminCustomerSaveAfter implements ObserverInterface
{
    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    protected $rewardsBalance;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Mirasvit\Rewards\Helper\Balance          $rewardsBalance
     * @param \Mirasvit\Rewards\Helper\Data             $rewardsData
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param ManagerInterface                          $messageManager
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Framework\AuthorizationInterface $authorization,
        ManagerInterface $messageManager
    ) {
        $this->rewardsBalance = $rewardsBalance;
        $this->rewardsData    = $rewardsData;
        $this->authorization  = $authorization;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        if (!$this->isAllowed()) {
            $this->messageManager->addSuccessMessage(
                __('You do not have permissions to create transactions. Please contact your site administrator.')
            );
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
            return;
        }
        $request = $observer->getEvent()->getRequest();
        $customer = $observer->getEvent()->getCustomer();
        $amount = (int) $request->getParam('rewards_change_balance');
        $message = $request->getParam('rewards_message');

        $emailMessage = '';
        if ($amount !== 0) {
            $amountBalace = $this->rewardsBalance->getBalancePoints($customer);
            if ($amountBalace + $amount < 0) {
                $amount = -$amountBalace;
            }

            if ($amount != 0) {
                $this->rewardsBalance->changePointsBalance(
                    $customer->getId(),
                    $amount, $message,
                    false,
                    true,
                    $emailMessage
                );
            }

            $formattedAmount = $this->rewardsData->formatPoints($amount);
            if ($amount > 0) {
                $alertMessage = __('%1 has been added.', $formattedAmount);
            } else {
                $alertMessage = __('%1 has been deducted.', $formattedAmount);
            }
            $this->messageManager->addSuccessMessage($alertMessage);
        }
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->authorization->isAllowed('Mirasvit_Rewards::reward_points_transaction');
    }
}
