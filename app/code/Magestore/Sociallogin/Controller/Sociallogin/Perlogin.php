<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;

class Perlogin extends \Magestore\Sociallogin\Controller\Sociallogin
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
        // url to verify
        $url = 'https://verifier.login.persona.org/verify';
        // get code verify
        $assert = $this->getRequest()->getParam('assertion');
        $params = 'assertion=' . urlencode($assert) . '&audience=' .
            urlencode($this->_storeManager->getStore()->getUrl());
        //send verify
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST => 2,
            CURLOPT_POSTFIELDS => $params,
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        $status = $this->_helperData->getPerResultStatus($result);
        if ($status == 'okay') {

            //get website_id and sote_id of each stores
            $store_id = $this->_storeManager->getStore()->getStoreId();
            $website_id = $this->_storeManager->getStore()->getWebsiteId();

            $email = $this->_helperData->getPerEmail($result);
            $name = explode("@", $email);
            $data = array('firstname' => $name[0], 'lastname' => $name[0], 'email' => $email);
            $customer = $this->_helperData->getCustomerByEmail($email, $website_id);
            if (!$customer || !$customer->getId()) {
                //Login multisite
                $customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
                if ($this->_helperData->getConfig('perlogin/is_send_password_to_customer')) {
                    $customer->sendPasswordReminderEmail();
                }
                if ($customer->getConfirmation()) {
                    try {
                        $customer->setConfirmation(null);
                        $customer->save();
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }

            $this->_getSession()->setCustomerAsLoggedIn($customer);
            $this->getResponse()->setRedirect($this->_loginPostRedirect());
        } else {
            $this->messageManager->addError('Login failed as you have not granted access.');
            $this->_redirect();
        }
    }

}
