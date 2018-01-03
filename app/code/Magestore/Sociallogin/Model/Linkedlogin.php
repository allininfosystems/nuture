<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Linkedlogin extends \Zend_Oauth_Consumer
{
    protected $_options = null;

    protected $_objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
        $this->_config = new \Zend_Oauth_Config;
        $this->_options = [
            'consumerKey' => $this->_objectManager->create('Magestore\Sociallogin\Helper\Data')->getLinkedConsumerKey(),
            'consumerSecret' => $this->_objectManager->create('Magestore\Sociallogin\Helper\Data')
                ->getLinkedConsumerSecret(),
            'version' => '1.0',
            'requestTokenUrl' => 'https://api.linkedin.com/uas/oauth/requestToken?scope=r_emailaddress',
            'accessTokenUrl' => 'https://api.linkedin.com/uas/oauth/accessToken',
            'authorizeUrl' => 'https://www.linkedin.com/uas/oauth/authenticate',
        ];

        $this->_config->setOptions($this->_options);
    }

    public function setCallbackUrl($url)
    {
        $this->_config->setCallbackUrl($url);
    }

    public function getOptions()
    {
        return $this->_options;
    }
}