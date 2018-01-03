<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model\ResourceModel;

class Vklogin extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {

        $this->_init('vklogin_customer', 'vk_customer_id');

    }
}
