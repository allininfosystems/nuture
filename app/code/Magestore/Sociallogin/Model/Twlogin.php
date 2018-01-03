<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Twlogin extends \Zend_Oauth_Consumer
{

    protected $_options = null;

    protected $_dataHelper;

    public function __construct(\Magestore\Sociallogin\Helper\Data $Datahelper)
    {
        $this->_dataHelper = $Datahelper;
        $this->_config = new \Zend_Oauth_Config;
        $this->_options = [
            'consumerKey' => $this->_dataHelper->getTwConsumerKey(),
            'consumerSecret' => $this->_dataHelper->getTwConsumerSecret(),
            'signatureMethod' => 'HMAC-SHA1',
            'version' => '1.0',
            'requestTokenUrl' => 'https://api.twitter.com/oauth/request_token',
            'accessTokenUrl' => 'https://api.twitter.com/oauth/access_token',
            'authorizeUrl' => 'https://api.twitter.com/oauth/authorize',
        ];

        $this->_config->setOptions($this->_options);
    }

    public function setCallbackUrl($url)
    {
        $this->_config->setCallbackUrl($url);
    }
}
