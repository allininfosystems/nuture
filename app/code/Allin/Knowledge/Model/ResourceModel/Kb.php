<?php
namespace Allin\Knowledge\Model\ResourceModel;

class Kb extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mst_kb_comment', 'id');
    }
}
?>