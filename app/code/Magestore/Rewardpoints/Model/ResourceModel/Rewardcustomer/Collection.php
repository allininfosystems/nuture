<?php namespace Magestore\Rewardpoints\Model\ResourceModel\Rewardcustomer;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Flat customer online grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends SearchResult
{
    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(array('customer_reward' => $this->getTable('rewardpoints_customer'))
            , 'main_table.entity_id = customer_reward.customer_id', array('point_balance'));
        $this->getSelect()->columns(['point_balance' => "IF(customer_reward.point_balance,customer_reward.point_balance,0)"]);
        return $this;
    }


    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param string|int|array|null $condition
     * @return \Magento\Cms\Model\ResourceModel\Block\Collection
    //     */
//    public function addFieldToFilter($field, $condition = null)
//    {
//        if ($field == 'point_balance') {
//            $field = 'customer_reward.point_balance';
//        }
//        return parent::addFieldToFilter($field, $condition);
//    }
//    public function orderRand(Magento\Framework\DB\Select $select, $field = null) {
//        if ($column == 'point_balance') {
//            $collection->getSelect()->order($column->getIndex() . ' ' . strtoupper($column->getDir()));
//        }
//        return orderRand($select, $field);
//    }
}
