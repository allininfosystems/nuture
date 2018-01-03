<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Fblogin extends Sociallogin
{

    public function newFacebook()
    {
        error_reporting(E_ALL ^ E_WARNING);
        error_reporting(E_ALL ^ E_NOTICE);
        try {
            require_once $this->_getBaseDir() . 'lib/Facebook/facebook.php';

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

        $facebook = new \Facebook(array(
            'appId' => $this->_dataHelper->getFbAppId(),
            'secret' => $this->_dataHelper->getFbAppSecret(),
            'cookie' => true,
        ));

        return $facebook;
    }

    public function getFbUser()
    {
        $facebook = $this->newFacebook();
        $userId = $facebook->getUser();

        $fbme = NULL;

        if ($userId) {

            try {
                $fbme = $facebook->api('/me?fields=email,first_name,last_name');
            } catch (\FacebookApiException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
            }
        }

        return $fbme;
    }

    public function getFbLoginUrl()
    {
        $facebook = $this->newFacebook();
        $loginUrl = $facebook->getLoginUrl(
            [
                'display' => 'popup',
                'redirect_uri' => $this->_dataHelper->getAuthUrl(),
                'scope' => 'email',
            ]
        );

        return $loginUrl;
    }
}
