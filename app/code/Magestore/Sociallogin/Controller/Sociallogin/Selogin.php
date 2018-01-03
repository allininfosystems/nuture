<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;

class Selogin extends \Magestore\Sociallogin\Controller\Sociallogin
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

        $se = $this->_objectManager->create('Magestore\Sociallogin\Model\Selogin')->newSe();
        $userId = $se->mode;

        if (!$userId) {

            try {

                $se_session = $this->_objectManager->create('Magestore\Sociallogin\Model\Selogin')->setSeIdlogin($se);

            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $url = $se_session->authUrl();
            $this->getResponse()->setRedirect($url);
        } else {

            if (!$se->validate()) {

                $se_session = $this->_objectManager->create('Magestore\Sociallogin\Model\Selogin')->setSeIdlogin($se);
                $url = $se_session->authUrl();
                $this->getResponse()->setRedirect($url);
            } else {

                $user_info = $se->getAttributes();

                if (count($user_info)) {
                    $frist_name = '';
                    $last_name = '';
                    $email = '';
                    if ($user_info['contact/email']) {

                        $email = $user_info['contact/email'];

                    } else if ($user_info['namePerson/first']) {

                        $frist_name = $user_info['namePerson/first'];

                    } else if ($user_info['namePerson/last']) {

                        $last_name = $user_info['namePerson/last'];

                    }

                    if (!$frist_name || !$last_name) {

                        if ($user_info['contact/email']) {
                            $email = explode("@", $email);
                            $frist_name = $email['0'];
                            $last_name = $email['0'];

                        } else if ($user_info['namePerson/friendly']) {
                            $frist_name = $user_info['namePerson/friendly'];
                            $last_name = $user_info['namePerson/friendly'];
                        }
                    }

                    //get website_id and sote_id of each stores
                    $store_id = $this->_storeManager->getStore()->getStoreId(); //add
                    $website_id = $this->_storeManager->getStore()->getWebsiteId(); //add

                    $data = [
                        'firstname' => $frist_name,
                        'lastname' => $last_name,
                        'email' => $user_info['contact/email'],
                    ];

                    $customer = $this->_helperData->getCustomerByEmail($data['email'], $website_id);
                    if (!$customer || !$customer->getId()) {
                        //Login multisite
                        $customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
                        if ($this->_helperData->getConfig('selogin/is_send_password_to_customer')) {
                            $customer->sendPasswordReminderEmail();
                        }
                        if ($customer->getConfirmation()) {
                            try {
                                $customer->setConfirmation(null);
                                $customer->save();
                            } catch (\Exception $e) {
                                $this->messageManager->addError('Error');
                            }
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
    }

}