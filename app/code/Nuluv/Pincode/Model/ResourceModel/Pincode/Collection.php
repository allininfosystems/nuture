<?php

namespace Nuluv\Pincode\Model\ResourceModel\Pincode;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Nuluv\Pincode\Model\Pincode', 'Nuluv\Pincode\Model\ResourceModel\Pincode');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>