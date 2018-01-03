<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Callogin extends Sociallogin
{

    const XML_PATH_CALLOGIN = 'sociallogin/sociallogin/callogin';
    const XML_PATH_CALLOGIN_USERDOMAIN = 'sociallogin/sociallogin/callogin';

    public function newCal()
    {
        try {
            require_once $this->_getBaseDir() . 'lib/OpenId/openid.php';

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

        $openid = new \LightOpenID($this->_storeManager->getStore()->getUrl());
        return $openid;
    }

    public function getCalLoginUrl($name_blog)
    {
        $cal_id = $this->newCal();
        $cal = $this->setCalIdlogin($cal_id, $name_blog);
        try {
            $loginUrl = $cal->authUrl();
            return $loginUrl;
        } catch (\Exception $e) {
            return null;
        }

    }

    public function setCalIdlogin($openid, $name_blog)
    {

        $openid->identity = 'https://' . $name_blog . '.clavid.com';
        $openid->required = [
            'namePerson/first',
            'namePerson/last',
            'namePerson/friendly',
            'contact/email',
        ];

        $openid->returnUrl = $this->_storeManager->getStore()->getUrl(self::XML_PATH_CALLOGIN);
        return $openid;
    }

}
