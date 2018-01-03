<?php
/**
 * Copyright © 2015 Bluethink. All rights reserved.
 */
namespace Bluethink\Productimport\Model\ResourceModel;

/**
 * Services resource
 */
class Import extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('productimport_import', 'id');
    }

  
}
