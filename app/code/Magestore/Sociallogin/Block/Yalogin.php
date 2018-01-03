<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Yalogin extends Sociallogin
{

    const XML_PATH_YALOGIN = 'sociallogin/sociallogin/yalogin';

    public function getLoginUrl()
    {
        return $this->getUrl(self::XML_PATH_YALOGIN);
    }

    protected function _beforeToHtml()
    {

        return parent::_beforeToHtml();
    }
}