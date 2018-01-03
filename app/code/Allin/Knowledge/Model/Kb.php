<?php
namespace Allin\Knowledge\Model;

class Kb extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Allin\Knowledge\Model\ResourceModel\Kb');
    }
}
?>