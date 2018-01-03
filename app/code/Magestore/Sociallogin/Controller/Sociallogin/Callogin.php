<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Callogin extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {

            $this->_login();

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    /**
     * getToken and call profile user Clavid
     **/
    public function _login($name_blog)
    {
        $cal = $this->getCalModel()->newCal();
        $userId = $cal->mode;
        if (!$userId) {
            $cal_session = $this->getCalModel()->setcalIdlogin($aol, $name_blog);
            $url = $cal_session->authUrl();
            echo "<script type='text/javascript'>top.location.href = '$url';</script>";
            exit;
        } else {
            if (!$cal->validate()) {
                $coreSession->addError('Login failed as you have not granted access.');
                die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
            } else {
                $user_info = $cal->getAttributes();
                if (count($user_info)) {
                    $frist_name = $user_info['namePerson/first'];
                    $last_name = $user_info['namePerson/last'];
                    $email = $user_info['contact/email'];
                    if (!$frist_name) {
                        if ($user_info['namePerson/friendly']) {
                            $frist_name = $user_info['namePerson/friendly'];
                        } else {
                            $email = explode("@", $email);
                            $frist_name = $email['0'];
                        }
                    }

                    if (!$last_name) {
                        $last_name = '_cal';
                    }

                    //get website_id and sote_id of each stores
                    $store_id = $this->_storeManager->getStore()->getStoreId(); //add
                    $website_id = $this->_storeManager->getStore()->getWebsiteId(); //add

                    $data = array('firstname' => $frist_name, 'lastname' => $last_name, 'email' => $user_info['contact/email']);
                    $customer = $this->_helperData->getCustomerByEmail($data['email'], $website_id); //add edition
                    if (!$customer || !$customer->getId()) {
                        //Login multisite
                        $customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
                        if ($this->_helperData->getConfig('callogin/is_send_password_to_customer')) {
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
    }

}