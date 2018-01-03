<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Orglogin extends Sociallogin
{

    const XML_PATH_ORGLOGIN = 'sociallogin/sociallogin/orglogin';

    public function newOrg()
    {
        try {
            require_once $this->_getBaseDir() . 'lib/OpenId/openid.php';

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

        $openid = new \LightOpenID($this->_storeManager->getStore()->getUrl());
        return $openid;
    }

    public function getOrgLoginUrl()
    {
        $aol_id = $this->newOrg();
        $aol = $this->setOrgIdlogin($aol_id);
        try {
            $loginUrl = $aol->authUrl();
            return $loginUrl;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setOrgIdlogin($openid)
    {

        $openid->identity = 'https://www.orange.fr';
        $openid->required = array(
            'namePerson/first',
            'namePerson/last',
            'namePerson/friendly',
            'contact/email',
        );

        $openid->returnUrl = $this->_storeManager->getStore()->getUrl(self::XML_PATH_ORGLOGIN);
        return $openid;
    }

}
