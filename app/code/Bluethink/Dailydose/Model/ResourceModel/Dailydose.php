<?php
/**
 * Copyright © 2015 Bluethink. All rights reserved.
 */
namespace Bluethink\Dailydose\Model\ResourceModel;

/**
 * Services resource
 */
class Dailydose extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('dailydose_love', 'id');
    }

  
}
