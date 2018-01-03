<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model\ResourceModel;

class Customer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {

        $this->_init('twlogin_customer', 'twitter_customer_id');

    }
}
