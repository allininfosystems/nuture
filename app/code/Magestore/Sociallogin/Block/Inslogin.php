<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Inslogin extends Sociallogin
{
    public function getInstagramLoginUrl()
    {
        return $this->_objectManager->create('Magestore\Sociallogin\Model\Instagramlogin')->getInstagramLoginUrl();
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