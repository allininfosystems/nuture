<?php namespace Magestore\Rewardpoints\Model\ResourceModel\Transaction;


/**
 * Flat customer online grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'transaction_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Rewardpoints\Model\Transaction', 'Magestore\Rewardpoints\Model\ResourceModel\Transaction');
    }
    /**
     * add availabel filter for transactions collection
     *
     * @return Magestore_RewardPoints_Model_Mysql4_Transaction_Collection
     */
    public function addAvailableBalanceFilter() {
        $this->getSelect()->where('point_amount > point_used');
        return $this;
    }

    /**
     * get total by field of this collection
     *
     * @param string $field
     * @return number
     */
    public function getFieldTotal($field = 'point_amount') {
        $this->_renderFilters();

        $sumSelect = clone $this->getSelect();
        $sumSelect->reset(\Zend_Db_Select::ORDER);
        $sumSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $sumSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $sumSelect->reset(\Zend_Db_Select::COLUMNS);

        $sumSelect->columns("SUM(`$field`)");

        return $this->getConnection()->fetchOne($sumSelect, $this->_bindParams);
    }

    public function getSelectCountSql() {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Zend_Db_Select::ORDER);
        $countSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(\Zend_Db_Select::COLUMNS);
        if (count($this->getSelect()->getPart(\Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(\Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(\Zend_Db_Select::GROUP);
            $countSelect->columns("COUNT(DISTINCT " . implode(", ", $group) . ")");
        } else {
            $countSelect->columns('COUNT(*)');
        }
        return $countSelect;
    }

}
