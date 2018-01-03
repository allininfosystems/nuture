<?php
namespace Nuluv\Lookingfor\Model\ResourceModel;

class Lookingfor extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lookingfor', 'id');
    }
}
?>