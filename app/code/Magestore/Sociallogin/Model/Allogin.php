<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;
class Allogin extends Sociallogin
{

    public function newAol()
    {
        try {
            require_once $this->_getBaseDir() . 'lib/OpenId/openid.php';
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

        $openid = new \LightOpenID($this->_storeManager->getStore()->getUrl());
        return $openid;
    }

    public function getAlLoginUrl($name)
    {
        $aol_id = $this->newAol();
        $aol = $this->setAolIdlogin($aol_id, $name);

        try {
            $loginUrl = $aol->authUrl();

            return $loginUrl;
        } catch (\Exception $e) {
            return null;
        }

    }

    public function setAolIdlogin($openid, $name)
    {

        $openid->identity = 'https://openid.aol.com/' . $name;
        $openid->required = array(
            'namePerson/first',
            'namePerson/last',
            'namePerson/friendly',
            'contact/email',
        );

        $openid->returnUrl = $this->_storeManager->getStore()->getUrl('sociallogin/sociallogin/allogin');
        return $openid;
    }

}
