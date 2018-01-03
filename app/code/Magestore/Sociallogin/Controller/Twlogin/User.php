<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Twlogin;

class User extends \Magestore\Sociallogin\Controller\Sociallogin
{

    /**
     *
     * @return void
     */
    public function execute()
    {

        try {

            $this->_user();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    //url after authorize
    public function _user()
    {
        $otwitter = $this->_objectManager->create('Magestore\Sociallogin\Model\Twlogin');
        $requestToken = $this->_getSingtone()->getRequestToken();

        $oauth_data = [
            'oauth_token' => $this->getRequest()->getParam('oauth_token'),
            'oauth_verifier' => $this->getRequest()->getParam('oauth_verifier'),
        ];
        try {
            $token = $otwitter->getAccessToken($oauth_data, unserialize($requestToken));
        } catch (\Exception $e) {
            $this->messageManager->addError('Login failed as you have not granted access.');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }
        $params = [
            'consumerKey' => $this->_helperData->getTwConsumerKey(),
            'consumerSecret' => $this->_helperData->getTwConsumerSecret(),
            'accessToken' => $token,
        ];

        $twitterId = $token->user_id;

        // get twitter account ID
        $customerId = $this->getCustomerId($twitterId);

        if ($customerId) {
            //login
            $customer = $this->_customerFactory->create()->load($customerId);

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

        } else {
            // redirect to login page

            $name = (string)$token->screen_name;
            $email = $name . '@twitter.com';
            $user['firstname'] = $name;
            $user['lastname'] = $name;
            $user['email'] = $email;

            //get website_id and sote_id of each stores
            $store_id = $this->_storeManager->getStore()->getStoreId();
            $website_id = $this->_storeManager->getStore()->getWebsiteId();
            $customer = $this->_helperData->getCustomerByEmail($user['email'], $website_id); //add edtition

            if (!$customer || !$customer->getData('entity_id')) {
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
            $this->setAuthorCustomer($twitterId, $customer->getData('entity_id'));
            $this->_getSingtone()->setCustomerIdSocialLogin($twitterId);

            $nextUrl = $this->_helperData->getEditUrl();
            $this->messageManager->addNotice('Please enter your contact detail.');
            die("<script>window.close();window.opener.location = '$nextUrl';</script>");
        }

    }

    //get customer id from twitter account if user connected
    public function getCustomerId($twitterId)
    {

        $customer = $this->_customerSocialCollectionFactory->create();
        $user = $customer->addFieldToFilter('twitter_id', $twitterId)
            ->getFirstItem();
        if ($user) {
            return $user->getData('customer_id');
        } else {
            return NULL;
        }

    }

    /**
     * input:
     * @mpId
     * @customerid
     **/
    public function setAuthorCustomer($twId, $customerId)
    {
        $mod = $this->_customerSocialFactory->create();
        $mod->setData('twitter_id', $twId);
        $mod->setData('customer_id', $customerId);
        $mod->save();
        return;
    }

    /**
     * return @collectin in model customer
     **/
    public function getCustomer($id)
    {
        $collection = $this->_customerFactory->create()->load($id);
        return $collection;
    }

}