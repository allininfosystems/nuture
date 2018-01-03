<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;

class Yalogin extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {

            $this->_login();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    // url to login
    public function _login()
    {

        $yalogin = $this->_objectManager->create('Magestore\Sociallogin\Model\Yalogin');

        $hasSession = $yalogin->hasSession();

        if ($hasSession == FALSE) {
            $authUrl = $yalogin->getAuthUrl();

            $this->getResponse()->setRedirect($authUrl);
        } else {

            $session = $yalogin->getSession();
            $userSession = $session->getSessionedUser();
            $profile = $userSession->loadProfile();
            if ($profile->emails) {
                $emails = $profile->emails;
            }

            $user = array();
            foreach ($emails as $email) {
                if (isset($email->primary) && $email->primary == true) {
                    if ($email->handle) {
                        $user['email'] = $email->handle;
                    }

                }

            }
            if ($profile->givenName) {
                $user['firstname'] = $profile->givenName;
            }

            if ($profile->familyName) {
                $user['lastname'] = $profile->familyName;
            }

            //get website_id and sote_id of each stores
            $store_id = $this->_storeManager->getStore()->getStoreId();
            $website_id = $this->_storeManager->getStore()->getWebsiteId();

            $customer = $this->_helperData->getCustomerByEmail($user['email'], $website_id);
            if (!$customer || !$customer->getId()) {
                //Login multisite
                $customer = $this->_helperData->createCustomerMultiWebsite($user, $website_id, $store_id);
                if ($this->_helperData->getConfig('yalogin/is_send_password_to_customer')) {
                    $customer->sendPasswordReminderEmail();
                }
            }

            // fix confirmation
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