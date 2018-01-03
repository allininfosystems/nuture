<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Popup;

class Login extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {

            $this->_login();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function _login()
    {
        //$sessionId = session_id();
        $username = $this->getRequest()->getPost('socialogin_email', false);
        $password = $this->getRequest()->getPost('socialogin_password', false);

        $result = ['success' => false];

        if ($username && $password) {
            try {
                // $this->_getSession()->login($username, $password);
                $login = $this->_objectManager->create('Magento\Customer\Api\AccountManagementInterface');
                $customer = $login->authenticate(
                    $username,
                    $password
                );
                $this->_getSession()->setCustomerDataAsLoggedIn($customer);
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
            }
            if (!isset($result['error'])) {
                $result['success'] = true;
            }
        } else {
            $result['error'] = __(
                'Please enter a username and password.');
        }
        $jsonEncode = $this->_objectManager->create('Magento\Framework\Json\Helper\Data');
        $this->getResponse()->setBody($jsonEncode->jsonEncode($result));
    }

}