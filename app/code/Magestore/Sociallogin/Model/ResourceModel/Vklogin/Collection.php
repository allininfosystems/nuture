<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model\ResourceModel\Vklogin;

/**
 * Collection Collection.
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(
            'Magestore\Sociallogin\Model\Vklogin',
            'Magestore\Sociallogin\Model\ResourceModel\Vklogin'
        );
    }
}
