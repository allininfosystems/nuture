<?php
/**
 * Copyright © 2015 Bluethink. All rights reserved.
 */
namespace Bluethink\Refferalcode\Model\ResourceModel;

/**
 * Services resource
 */
class Rewardpoint extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('custom_refferalcode', 'id');
    }

  
}
