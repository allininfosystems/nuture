<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Amazon extends Sociallogin
{

    protected function _beforeToHtml()
    {

        return parent::_beforeToHtml();
    }

    public function getAmazonCustomerKey()
    {
        return $this->_dataHelper->getAmazonConsumerKey();
    }
}