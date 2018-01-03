<?php
/**
 * Copyright © 2015 Bluethink. All rights reserved.
 */
namespace Bluethink\Faq\Model\ResourceModel;

/**
 * Faq resource
 */
class Faq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('faq_faq', 'id');
    }

  
}
