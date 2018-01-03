<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\linkedlogin;
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

    public function _user()
    {
        $linkedlogin = $this->_objectManager->create('Magestore\Sociallogin\Model\Linkedlogin');

        $oauth_data = array(
            'oauth_token' => $this->getRequest()->getParam('oauth_token'),
            'oauth_verifier' => $this->getRequest()->getParam('oauth_verifier'),
        );

        $requestToken = $this->_getSingtone()->getRequestToken(array('scope' => 'r_emailaddress'));
        try {
            $accessToken = $linkedlogin->getAccessToken($oauth_data, unserialize($requestToken));
        } catch (\Exception $e) {
            $this->messageManager->addError('User has not shared information so login fail!');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_loginPostRedirect() . "\"} window.close();</script>");
        }
        $oauthOptions = $linkedlogin->getOptions();
        $options = $oauthOptions;
        $client = $accessToken->getHttpClient($options);
        $client->setUri('http://api.linkedin.com/v1/people/~');
        $client->setMethod(\Zend_Http_Client::GET);
        $response = $client->request();
        $content = $response->getBody();
        $xml = simplexml_load_string($content);
        $user = array();
        $firstname = (string)$xml->{'first-name'};
        $lastname = (string)$xml->{'last-name'};
        $client2 = $accessToken->getHttpClient($options);
        $client2->setUri('http://api.linkedin.com/v1/people/~/email-address');
        $client2->setMethod(\Zend_Http_Client::GET);
        $response2 = $client2->request();
        $content2 = $response2->getBody();
        $xml2 = simplexml_load_string($content2);
        $email = (string)$xml2->{0};
        if (!$email) {
            $email = $firstname . $lastname . "@linkedin.com";
        }

        $user['firstname'] = $firstname;
        $user['lastname'] = $lastname;
        $user['email'] = $email;

        //get website_id and sote_id of each stores
        $store_id = $this->_storeManager->getStore()->getStoreId();
        $website_id = $this->_storeManager->getStore()->getWebsiteId();

        $customer = $this->_helperData->getCustomerByEmail($email, $website_id); //add edition
        if (!$customer || !$customer->getId()) {
            //Login multisite
            $customer = $this->_helperData->createCustomerMultiWebsite($user, $website_id, $store_id);
            if ($this->_helperData->getConfig('linklogin/is_send_password_to_customer')) {
                $customer->sendPasswordReminderEmail();
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
            $this->_getSingtone()->setCustomerAsLoggedIn($customer);
            die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"" . $this->_loginPostRedirect() . "\";}else{try{window.opener.location.href=\"" . $this->_loginPostRedirect() . "\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
        } else {

            $getConfirmPassword = (int)$this->_helperData->getConfig('linklogin/is_customer_confirm_password');
            if ($getConfirmPassword) {
                die("
				<script type=\"text/javascript\">
				var email = ' $email ';
				window.opener.opensocialLogin();
				window.opener.document.getElementById('magestore-sociallogin-popup-email').value = email;
				window.close();</script>  ");
            } else {
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

}