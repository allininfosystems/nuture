<?php
/**
 * Copyright © 2015 Nuluv. All rights reserved.
 */
namespace Nuluv\Liker\Model\ResourceModel;

/**
 * Liker resource
 */
class Liker extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('liker_liker', 'id');
    }

  
}
