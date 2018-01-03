<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Openlogin extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {

            $this->_login();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function getOpenModel()
    {
        return $this->_objectManager->create('Magestore\Sociallogin\Model\Openlogin');
    }

    public function _login()
    {
        $identity = $this->getRequest()->getPost('identity');

        $my = $this->getOpenModel()->newMy();
        $this->_getSingtone()->setData('identity', $identity);

        $userId = $my->mode;

        if (!$userId) {
            $my = $this->getOpenModel()->setOpenIdlogin($my, $identity);

            try {
                $url = $my->authUrl();

            } catch (\Exception $e) {

                $this->messageManager->addError('Username not exacted');
                die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
            }
            echo "<script type='text/javascript'>top.location.href = '$url';</script>";
            exit;
        } else {
            if (!$my->validate()) {
                $my = $this->getOpenModel()->setOpenIdlogin($my, $identity);
                try {
                    $url = $my->authUrl();
                } catch (\Exception $e) {
                    $this->messageManager->addError('Username not exacted');
                    die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
                }
                echo "<script type='text/javascript'>top.location.href = '$url';</script>";
                exit;
            } else {
                $user_info = $my->data;
                if (count($user_info)) {
                    $user = array();
                    $identity = $user_info['openid_identity'];
                    $length = strlen($identity);
                    $httpLen = strlen("http://");
                    $userAccount = substr($identity, $httpLen, $length - 1 - $httpLen);
                    $userArray = explode('.', $userAccount, 2);
                    $firstname = $userArray[0];
                    $lastname = "";
                    $email = $firstname . "@" . $userArray[1];
                    $authorId = $email;
                    $user['firstname'] = $firstname;
                    $user['lastname'] = $lastname;
                    $user['email'] = $email;
                    //get website_id and sote_id of each stores
                    $store_id = $this->_storeManager->getStore()->getStoreId();
                    $website_id = $this->_storeManager->getStore()->getWebsiteId();
                    $customer = $this->_helperData->getCustomerByEmail($email, $website_id);
                    if (!$customer || !$customer->getId()) {
                        //Login multisite
                        $customer = $this->_helperData->createCustomerMultiWebsite($user, $website_id, $store_id);
                    }
                    $this->_objectManager->create('Magestore\Sociallogin\Model\Authorlogin')->addCustomer($authorId);
                    if ($this->_helperData->getConfig('openlogin/is_send_password_to_customer')) {
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
                    $this->_getSession()->setCustomerAsLoggedIn($customer);
                    $nextUrl = $this->_helperData->getEditUrl();
                    die("<script>window.close();window.opener.location = '$nextUrl';</script>");

                } else {
                    $this->messageManager->addError('User has not shared information so login fail!');
                    die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
                }
            }
        }
    }

}