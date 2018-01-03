<?php
namespace Nuluv\Lookingfor\Model;

class Lookingfor extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Nuluv\Lookingfor\Model\ResourceModel\Lookingfor');
    }
}
?>