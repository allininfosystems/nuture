<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Orglogin extends Sociallogin
{
    public function getLoginUrl()
    {
        return $this->getUrl('sociallogin/sociallogin/orglogin');
    }

    public function getAlLoginUrl()
    {
        return $this->_objectManager->create('Magestore\Sociallogin\Model\Orglogin')->getOrgLoginUrl();
    }
}