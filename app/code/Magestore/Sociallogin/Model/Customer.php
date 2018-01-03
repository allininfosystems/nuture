<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Customer extends Sociallogin
{

    public function _construct()
    {
        $this->_init('Magestore\Sociallogin\Model\ResourceModel\Customer');
    }

}