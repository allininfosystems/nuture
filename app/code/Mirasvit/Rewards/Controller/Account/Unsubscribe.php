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


namespace Mirasvit\Rewards\Controller\Account;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class Unsubscribe extends \Mirasvit\Rewards\Controller\Account
{
    public function __construct(
        CustomerRepository $customerRepository,
        \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->customerRepository = $customerRepository;

        parent::__construct($transactionFactory, $registry, $customerSession, $context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId === null) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
        } else {
            $customer = $this->customerRepository->getById($customerId);
            $customer->setCustomAttribute('rewards_subscription', 0);

            $this->customerRepository->save($customer);

            $this->messageManager
                ->addSuccessMessage(__('We removed your subscription on expiring ponits notification.'));
        }

        $this->_redirect('customer/account/');
    }
}
