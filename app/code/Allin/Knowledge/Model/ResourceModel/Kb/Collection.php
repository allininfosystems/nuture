<?php

namespace Allin\Knowledge\Model\ResourceModel\Kb;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Allin\Knowledge\Model\Kb', 'Allin\Knowledge\Model\ResourceModel\Kb');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>