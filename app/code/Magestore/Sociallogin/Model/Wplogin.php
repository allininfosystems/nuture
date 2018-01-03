<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Wplogin extends Sociallogin
{

    const XML_PATH_WPLOGIN = 'sociallogin/sociallogin/wplogin';

    public function newWp()
    {
        try {
            require_once $this->_getBaseDir() . 'lib/OpenId/openid.php';

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

        $openid = new \LightOpenID($this->_storeManager->getStore()->getUrl());
        return $openid;
    }

    public function getWpLoginUrl($name_blog)
    {
        $wp_id = $this->newWp();
        $wp = $this->setWpIdlogin($wp_id, $name_blog);
        try {
            $loginUrl = $wp->authUrl();
            return $loginUrl;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setWpIdlogin($openid, $name_blog)
    {

        $openid->identity = 'http://' . $name_blog . '.wordpress.com';
        $openid->required = [
            'namePerson/first',
            'namePerson/last',
            'namePerson/friendly',
            'contact/email',
        ];

        $openid->returnUrl = $this->_storeManager->getStore()->getUrl(self::XML_PATH_WPLOGIN);
        return $openid;
    }
}
