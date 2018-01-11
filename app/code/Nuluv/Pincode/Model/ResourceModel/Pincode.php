<?php
namespace Nuluv\Pincode\Model\ResourceModel;

class Pincode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pincodelist', 'id');
    }
}
?>