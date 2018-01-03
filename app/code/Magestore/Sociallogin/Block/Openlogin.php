<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Openlogin extends Sociallogin
{
    public function getLoginUrl()
    {
        return $this->getUrl('sociallogin/sociallogin/openlogin');
    }

    public function getSetBlock()
    {
        return $this->getUrl('sociallogin/openlogin/setBlock');
    }

    public function setBackUrl()
    {
        $currentUrl = $this->getCurrentUrl();
        $this->_getSingtoneSession()->setBackUrl($currentUrl);
        return $currentUrl;
    }

    protected function _beforeToHtml()
    {

        return parent::_beforeToHtml();
    }
}