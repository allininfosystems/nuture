<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Fqlogin extends \Magestore\Sociallogin\Controller\Sociallogin {

	public function execute() {

		try {

			$this->_login();
		} catch (\Exception $e) {

			$this->messageManager->addError($e->getMessage());
		}

	}

	/**
	 * getToken and call profile user FoursQuare
	 **/
	public function _login() {
		$isAuth = $this->getRequest()->getParam('auth');
		$foursquare = $this->_objectManager->create('Magestore\Sociallogin\Model\Fqlogin')->newFoursquare();
		$code = $this->getRequest()->getParam('code');
		$date = date('Y-m-d');
		$date = str_replace('-', '', $date);
		$oauth = $foursquare->GetToken($code);

		if (!$oauth) {
			echo ("<script>window.close()</script>");
			return;
		}
		$url = 'https://api.foursquare.com/v2/users/self?oauth_token=' . $oauth . '&v=' . $date;
		try {
			$json = $this->_helperData->getResponseBody($url);
		} catch (\Exception $e) {
			$this->messageManager->addError('Login fail!');
			die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
		}
		$string = $foursquare->getResponseFromJsonString($json);

		$first_name = $string->user->firstName;
		$last_name = $string->user->firstName;

		$email = $string->user->contact->email;

		if ($isAuth && $oauth) {

			//get website_id and sote_id of each stores
			$store_id = $this->_storeManager->getStore()->getStoreId(); //add
			$website_id = $this->_storeManager->getStore()->getWebsiteId(); //add

			$data = array('firstname' => $first_name, 'lastname' => $last_name, 'email' => $email);
			$customer = $this->_helperData->getCustomerByEmail($data['email'], $website_id); //add edition
			if (!$customer || !$customer->getId()) {
				//Login multisite
				$customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
				if ($this->_helperData->getConfig('fqlogin/is_send_password_to_customer')) {
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
				die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"" . $this->_loginPostRedirect() . "\";}else{try{window.opener.location.href=\"" . $this->_loginPostRedirect() . "\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
			} else {
				$getConfirmPassword = (int) $this->_helperData->getConfig('fqlogin/is_customer_confirm_password');
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

}