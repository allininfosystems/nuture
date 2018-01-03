<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

const XML_PATH_GOLOGIN = 'sociallogin/gologin/user';

class Gologin extends Sociallogin
{
    public function Gonew()
    {

        require_once $this->_getBaseDir() . 'lib/Oauth2/service/Google_ServiceResource.php';
        require_once $this->_getBaseDir() . 'lib/Oauth2/service/Google_Service.php';
        require_once $this->_getBaseDir() . 'lib/Oauth2/service/Google_Model.php';
        require_once $this->_getBaseDir() . 'lib/Oauth2/contrib/Google_Oauth2Service.php';
        require_once $this->_getBaseDir() . 'lib/Oauth2/Google_Client.php';

        $this->_config = new \Google_Client;
        $this->_config->setClientId($this->_dataHelper->getGoConsumerKey());
        $this->_config->setClientSecret($this->_dataHelper->getGoConsumerSecret());
        $this->_config->setRedirectUri($this->_storeManager->getStore()->getUrl('sociallogin/gologin/user', ['_secure' => true]));
        return $this->_config;
    }
}
