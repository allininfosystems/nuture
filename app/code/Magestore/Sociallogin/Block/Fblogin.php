<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Fblogin extends Sociallogin
{

    public function getFbmodel()
    {
        return $this->_objectManager->create('Magestore\Sociallogin\Model\Fblogin');
    }

    public function getFbUser()
    {
        return $this->getFbmodel()->getFbUser();
    }

    public function getFbLoginUrl()
    {
        return $this->getFbmodel()->getFbLoginUrl();
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