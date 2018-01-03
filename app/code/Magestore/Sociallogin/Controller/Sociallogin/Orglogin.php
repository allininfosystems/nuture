<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Orglogin extends \Magestore\Sociallogin\Controller\Sociallogin
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
        $org = $this->_objectManager->create('Magestore\Sociallogin\Model\Orglogin')->newOrg();
        $user_info = $org->data;
        if (count($user_info)) {
            $frist_name = $user_info['openid_sreg_nickname'];
            $last_name = $user_info['openid_sreg_nickname'];
            $email = $user_info['openid_sreg_email'];

            //get website_id and sote_id of each stores
            $store_id = $this->_storeManager->getStore()->getStoreId(); //add
            $website_id = $this->_storeManager->getStore()->getWebsiteId(); //add

            $data = array('firstname' => $frist_name, 'lastname' => $last_name, 'email' => $email);

            $customer = $this->_helperData->getCustomerByEmail($data['email'], $website_id);
            if (!$customer || !$customer->getId()) {
                //Login multisite
                $customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
                if ($this->_helperData->getConfig('orglogin/is_send_password_to_customer')) {
                    $customer->sendPasswordReminderEmail();
                }
            }
            if ($customer->getConfirmation()) {
                try {
                    $customer->setConfirmation(null);
                    $customer->save();
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
            $this->_getSession()->setCustomerAsLoggedIn($customer);
            die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"" . $this->_loginPostRedirect() . "\";}else{try{window.opener.location.href=\"" . $this->_loginPostRedirect() . "\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
        } else {
            $this->messageManager->addError('Login failed as you have not granted access.');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }
    }

}