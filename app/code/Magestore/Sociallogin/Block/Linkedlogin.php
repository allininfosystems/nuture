<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Linkedlogin extends Sociallogin
{
    public function getLoginUrl()
    {
        return $this->getUrl('sociallogin/sociallogin/linkedlogin');
    }

    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }
}