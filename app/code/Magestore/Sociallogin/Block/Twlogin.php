<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Twlogin extends Sociallogin
{
    const XML_PATH_TWLOGIN = 'sociallogin/sociallogin/twlogin';

    public function getLoginUrl()
    {
        return $this->getUrl(self::XML_PATH_TWLOGIN);
    }

    public function setBackUrl()
    {
        $currentUrl = $this->getCurrentUrl();
        $this->_session->setBackUrl($currentUrl);
        return $currentUrl;
    }

    protected function _beforeToHtml()
    {

        return parent::_beforeToHtml();
    }
}