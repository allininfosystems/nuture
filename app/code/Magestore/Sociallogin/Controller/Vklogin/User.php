<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Vklogin;

class User extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {
            $this->_user();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function getVkModel()
    {
        return $this->_objectManager->create('Magestore\Sociallogin\Model\Vklogin');
    }

    public function getCustomerId($vkId)
    {

        $customer = $this->_vkloginCollectionFactory->create();

        $user = $customer->addFieldToFilter('vk_id', $vkId)
            ->getFirstItem();

        if ($user) {
            return $user->getData('customer_id');
        } else {
            return NULL;
        }

    }

    public function setAuthorCustomer($vkId, $customerId)
    {
        $mod = $this->getVkModel();
        $mod->setData('vk_id', $vkId);
        $mod->setData('customer_id', $customerId);
        $mod->save();
        return;
    }

    public function _user()
    {
        $vklogin = $this->getVkModel()->getVk();

        $code = $this->getRequest()->getParam('code');
        if (!$code) {
            $this->messageManager->addError('Login failed as you have not granted access.');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }
        $redirectUrl = $this->_storeManager->getStore()->getBaseUrl() . 'sociallogin/vklogin/user';

        $accessToken = $vklogin->getAccessToken($code, $redirectUrl);

        $userId = $accessToken['user_id'];

        $users = $vklogin->api(
            'getProfiles',
            [
                'uids' => $userId,
                'fields' => 'first_name, last_name',
            ]

        );

        foreach ($users['response'] as $userVk) {
            $user = $userVk;
            break;
        }
        $vkId = $user['uid'];

        $customerId = $this->getCustomerId($vkId);

        //get website_id and sote_id of each stores
        $store_id = $this->_storeManager->getStore()->getStoreId();
        $website_id = $this->_storeManager->getStore()->getWebsiteId();

        if ($customerId) {
            $customer = $this->_customerFactory->create()->load($customerId);
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

            $user['firstname'] = $user['first_name'];
            $user['lastname'] = $user['last_name'];
            $name = $user['firstname'] . $user['lastname'];
            $email = $name . '@vk.com';
            $user['email'] = $email;
            //get website_id and sote_id of each stores
            $customer = $this->_helperData->getCustomerByEmail($user['email'], $website_id); //add edtition
            if (!$customer || !$customer->getId()) {
                //Login multisite
                $customer = $this->_helperData->createCustomerMultiWebsite($user, $website_id, $store_id);
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
            $this->setAuthorCustomer($vkId, $customer->getId());
            $this->_getSingtone()->setCustomerIdSocialLogin($vkId);
            if ($this->_helperData->getConfig('vklogin/is_send_password_to_customer')) {
                $customer->sendPasswordReminderEmail();
            }
            $nextUrl = $this->_helperData->getEditUrl();
            $this->messageManager->addNotice('Please enter your contact detail.');
            die("<script>window.close();window.opener.location = '$nextUrl';</script>");
        }
    }

}