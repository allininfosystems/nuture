<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Popup;

use Magento\Customer\Model\AccountManagement;

class SendPass extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {

            $this->_sendPass();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function _sendPass()
    {
        $email = $this->getRequest()->getPost('socialogin_email_forgot', false);
        $model = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customer = $model->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())
            ->loadByEmail($email);

        if ($customer->getId()) {
            try {
                $newPass = $this->_objectManager->create('Magento\Customer\Api\AccountManagementInterface');
                $newPass->initiatePasswordReset(
                    $email,
                    AccountManagement::EMAIL_RESET
                );
                // $newPassword = $customer->generatePassword();
                // $customer->changePassword($newPassword, false);
                // $customer->sendPasswordReminderEmail();
                $result = ['success' => true];
            } catch (\Exception $e) {
                $result = ['success' => false, 'error' => $e->getMessage()];
            }
        } else {
            $result = ['success' => false, 'error' => 'Not found!'];
        }
        // $this->messageManager->addSuccess(__('We\'ll email you a link to reset your password.'));
        $jsonEncode = $this->_objectManager->create('Magento\Framework\Json\Helper\Data');
        $this->getResponse()->setBody($jsonEncode->jsonEncode($result));
    }

}