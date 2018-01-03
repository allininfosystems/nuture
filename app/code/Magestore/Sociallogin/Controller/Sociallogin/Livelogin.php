<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Livelogin extends \Magestore\Sociallogin\Controller\Sociallogin
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
        $isAuth = $this->getRequest()->getParam('auth');
        $code = $this->getRequest()->getParam('code');
        $live = $this->_objectManager->create('Magestore\Sociallogin\Model\Livelogin')->newLive();
        try {
            $json = $live->authenticate($code);
            $user = $live->get("me", $live->param);
        } catch (\Exception $e) {
            $this->messageManager->addError('Login failed as you have not granted access.');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->emails->account;
        //get website_id and sote_id of each stores
        $store_id = $this->_storeManager->getStore()->getStoreId(); //add
        $website_id = $this->_storeManager->getStore()->getWebsiteId(); //add

        if ($isAuth) {
            $data = array('firstname' => $first_name, 'lastname' => $last_name, 'email' => $email);
            $customer = $this->_helperData->getCustomerByEmail($data['email'], $website_id); //add edtition
            if (!$customer || !$customer->getId()) {
                //Login multisite
                $customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
                if ($this->_helperData->getConfig('livelogin/is_send_password_to_customer')) {
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
        }
    }

}