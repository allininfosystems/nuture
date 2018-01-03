<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Livelogin extends Sociallogin
{
    public function getLoginUrl()
    {
        return $this->getUrl('sociallogin/sociallogin/fqlogin');
    }

    public function getFqUser()
    {
        return $this->_objectManager->create('Magestore\Sociallogin\Model\Fqlogin')->getFqUser();
    }

    public function getUrlAuthorCode()
    {
        return $this->_objectManager->create('Magestore\Sociallogin\Model\Livelogin')->getUrlAuthorCode();
    }

    public function getDirectLoginUrl()
    {
        return $this->_dataHelper->getDirectLoginUrl();
    }

    protected function _beforeToHtml()
    {

        return parent::_beforeToHtml();
    }

}