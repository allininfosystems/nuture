<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Amazonlogin extends \Magestore\Sociallogin\Controller\Sociallogin
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

        $amazon = $this->_objectManager->create('Magestore\Sociallogin\Model\Amazon');
        $token = $this->getRequest()->getParam('token', false);
        if (!$token) {
            $this->messageManager->addError('You provided a email invalid!');
            die("<script type=\"text/javascript\">try{window.location.reload(true);}catch(e){window.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"}</script>");
            return;
        }
        // get profile
        $profile = $amazon->getUserProfileFromAccessToken($token);
        if ($profile && $profile->user_id) {
            $store_id = $this->_storeManager->getStore()->getStoreId(); //add
            $website_id = $this->_storeManager->getStore()->getWebsiteId(); //add
            $data = array();
            if (false === strpos($profile->name, ' ')) {
                $len = round(strlen($profile->name) / 2);
                $data['firstname'] = substr($profile->name, 0, $len);
                $data['lastname'] = substr($profile->name, $len);
            } else {
                $list = explode(' ', $profile->name);
                $data['lastname'] = array_pop($list);
                $data['firstname'] = implode(' ', $list);
            }
            $data['email'] = $profile->email;
            if ($data['email']) {
                $customer = $this->_helperData->getCustomerByEmail($data['email'], $website_id); //add edition
                if (!$customer || !$customer->getId()) {
                    //Login multisite
                    $customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
                    if ($this->_helperData->getConfig('amazonlogin/is_send_password_to_customer')) {
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
                die("<script type=\"text/javascript\">try{window.location.href=\"" . $this->_loginPostRedirect() . "\";}catch(e){window.location.reload(true);}</script>");
            } else {
                $this->messageManager->addError('You provided a email invalid!');
                die("<script type=\"text/javascript\">try{window.location.reload(true);}catch(e){window.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"}</script>");
            }
        }
    }

}