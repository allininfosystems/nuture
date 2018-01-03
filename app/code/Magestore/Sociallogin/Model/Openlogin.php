<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Openlogin extends Sociallogin
{

    const XML_PATH_OPENLOGIN = 'sociallogin/sociallogin/openlogin';

    public function newMy()
    {
        try {
            require_once $this->_getBaseDir() . 'lib/OpenId/openid.php';

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
        $openid = new \LightOpenID($this->_storeManager->getStore()->getUrl());
        return $openid;
    }

    public function getOpenLoginUrl($identity)
    {
        $my_id = $this->newMy();
        $my = $this->setOpenIdlogin($my_id, $identity);
        $loginUrl = $my->authUrl();
        return $loginUrl;
    }

    public function setOpenIdlogin($openid, $identity)
    {
        $openid->identity = "http://" . $identity . ".myopenid.com";
        $openid->required = array(
            'namePerson/first',
            'namePerson/last',
            'namePerson/friendly',
            'contact/email',
            'namePerson',
        );
        $openid->returnUrl = $this->_storeManager->getStore()->getUrl(self::XML_PATH_OPENLOGIN);
        return $openid;
    }
}
