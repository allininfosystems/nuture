<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Vklogin extends Sociallogin
{

    const XML_PATH_VKLOGIN = 'sociallogin/sociallogin/vklogin';

    public function getLoginUrl()
    {
        return $this->getUrl(self::XML_PATH_VKLOGIN);
    }

    protected function _beforeToHtml()
    {

        return parent::_beforeToHtml();
    }
}