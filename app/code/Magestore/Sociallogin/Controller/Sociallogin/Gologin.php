<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;

class Gologin extends \Magestore\Sociallogin\Controller\Sociallogin
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

        if (!$this->getAuthorizedToken()) {

            $token = $this->getAuthorization();

        } else {

            $token = $this->getAuthorizedToken();
        }

        return $token;
    }

    // if exit access token
    public function getAuthorizedToken()
    {
        $token = false;
        if (!is_null($this->_getSingtone()->getAccessToken())) {
            $token = unserialize($this->_getSingtone()->getAccessToken());
        }
        return $token;
    }

    // if not exit access token
    public function getAuthorization()
    {

        $scope = [
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/userinfo.email',
        ];
        $gologin = $this->_objectManager->create('Magestore\Sociallogin\Model\Gologin')->Gonew();
        $gologin->setScopes($scope);

        $gologin->authenticate();

        $authUrl = $gologin->createAuthUrl();
        header('Localtion: ' . $authUrl);
        die(1);
    }

}