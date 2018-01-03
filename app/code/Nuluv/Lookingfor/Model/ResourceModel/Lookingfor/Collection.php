<?php

namespace Nuluv\Lookingfor\Model\ResourceModel\Lookingfor;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Nuluv\Lookingfor\Model\Lookingfor', 'Nuluv\Lookingfor\Model\ResourceModel\Lookingfor');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>