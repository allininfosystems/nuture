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



namespace Mirasvit\Rewards\Controller\Referral;

use Magento\Framework\Controller\ResultFactory;

class Post extends \Mirasvit\Rewards\Controller\Referral
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $customer = $this->customer->getCurrentCustomer();
        $names = $this->getRequest()->getParam('name');
        $emails = $this->getRequest()->getParam('email', []);
        $invitations = [];
        foreach ($emails as $key => $email) {
            if (empty($email) || empty($names[$key])) {
                continue;
            }
            $invitations[$email] = $names[$key];
        }
        $message = $this->getRequest()->getParam('message');
        $rejectedEmails = $this->rewardsReferral->frontendPost($customer, $invitations, $message);
        if (count($rejectedEmails)) {
            foreach ($rejectedEmails as $email) {
                $this->messageManager->addNoticeMessage(
                    __('Customer with email %1 has been already invited to our store', $email)
                );
            }
        }
        if (count($rejectedEmails) < count($invitations)) {
            $this->messageManager->addSuccessMessage(__('Your invitations were sent. Thanks!'));
        }
        $this->_redirect('*/*/');
    }
}
