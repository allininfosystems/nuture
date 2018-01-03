<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Wplogin extends Sociallogin
{
    const XML_PATH_WPLOGIN_SETBLOCk = 'sociallogin/wplogin/setblock';
    const XML_PATH_WPLOGIN = 'sociallogin/sociallogin/wplogin';

    public function getLoginUrl()
    {
        return $this->getUrl(self::XML_PATH_WPLOGIN);
    }

    public function getAlLoginUrl()
    {
        return $this->getUrl(self::XML_PATH_WPLOGIN_SETBLOCk);
    }

    public function getCheckName()
    {
        return $this->getUrl(self::XML_PATH_WPLOGIN_SETBLOCk);
    }

    public function getEnterName()
    {
        return 'ENTER YOUR BLOG NAME';
    }

    public function getName()
    {
        return 'Name';
    }
}