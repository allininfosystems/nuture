<?php
namespace Nuluv\Pincode\Model;

class Pincode extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Nuluv\Pincode\Model\ResourceModel\Pincode');
    }
}
?>