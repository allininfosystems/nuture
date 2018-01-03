<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Selogin extends Sociallogin
{

    const XML_PATH_SELOGIN = 'sociallogin/sociallogin/selogin';

    public function newSe()
    {
        try {
            require_once $this->_getBaseDir() . 'lib/OpenId/openid.php';
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

        $openid = new \LightOpenID($this->_storeManager->getStore()->getUrl());
        return $openid;
    }

    public function setSeIdlogin($openid)
    {
        $openid->identity = 'https://openid.stackexchange.com';
        $openid->required = [
            'namePerson/first',
            'namePerson/last',
            'namePerson/friendly',
            'contact/email',
        ];

        $openid->returnUrl = $this->_storeManager->getStore()->getUrl(self::XML_PATH_SELOGIN);
        return $openid;
    }

}
