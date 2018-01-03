<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Fqlogin extends Sociallogin
{
    public function getLoginUrl()
    {
        return $this->getUrl('sociallogin/sociallogin/fqlogin');
    }

    public function getFqModel()
    {
        return $this->_objectManager->create('Magestore\Sociallogin\Model\Fqlogin');
    }

    public function getFqUser()
    {
        return $this->getFqModel()->getFqUser();
    }

    public function getFqLoginUrl()
    {
        return $this->getFqModel()->getFqLoginUrl();
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