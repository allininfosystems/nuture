<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Perlogin extends Sociallogin
{
    protected function _beforeToHtml()
    {

        return parent::_beforeToHtml();
    }

    public function getPerLoginUrl()
    {
        return $this->getUrl('sociallogin/sociallogin/perlogin');
    }
}