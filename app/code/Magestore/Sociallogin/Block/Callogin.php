<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Callogin extends Sociallogin
{
    public function getLoginUrl()
    {
        return $this->getUrl('sociallogin/sociallogin/callogin');
    }

    public function getAlLoginUrl()
    {
        return $this->getUrl('sociallogin/callogin/setClaivdName');
    }

    public function getCheckName()
    {
        return $this->getUrl('sociallogin/callogin/setblock');
    }

    public function getName()
    {
        return 'Name';
    }

    public function getEnterName()
    {
        return 'ENTER YOUR CLAVID NAME';
    }

    protected function _beforeToHtml()
    {

        return parent::_beforeToHtml();
    }
}