<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Aollogin extends Sociallogin
{
    const XML_PATH_ALLOGIN = 'sociallogin/sociallogin/allogin';
    const XML_PATH_ALLOGIN_CREENNAME = 'sociallogin/allogin/setScreenName';
    const XML_PATH_ALLOGIN_SETBLOCK = 'sociallogin/allogin/setblock';

    public function getLoginUrl()
    {
        return $this->getUrl(self::XML_PATH_ALLOGIN);
    }

    public function getAlLoginUrl()
    {
        return $this->getUrl(self::XML_PATH_ALLOGIN_CREENNAME);
    }

    public function getEnterName()
    {
        return 'ENTER SCREEN NAME';
    }

    public function getName()
    {
        return 'Name';
    }

    public function getCheckName()
    {
        return $this->getUrl(self::XML_PATH_ALLOGIN_SETBLOCK);
    }

    protected function _beforeToHtml()
    {

        return parent::_beforeToHtml();
    }
}