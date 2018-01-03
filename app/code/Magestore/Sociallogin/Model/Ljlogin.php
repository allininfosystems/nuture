<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Ljlogin extends Sociallogin
{

    const XML_PATH_LJLOGIN = 'sociallogin/sociallogin/ljlogin';

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

    public function getLjLoginUrl($identity)
    {
        $my_id = $this->newMy();
        $my = $this->setLjIdlogin($my_id, $identity);
        $loginUrl = $my->authUrl();
        return $loginUrl;
    }

    public function setLjIdlogin($openid, $identity)
    {
        $openid->identity = "http://" . $identity . ".livejournal.com";
        $openid->required = array(
            'namePerson/first',
            'namePerson/last',
            'namePerson/friendly',
            'contact/email',
        );
        $openid->returnUrl = $this->_storeManager->getStore()->getUrl(self::XML_PATH_LJLOGIN);
        return $openid;
    }
}
