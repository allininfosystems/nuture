<?php namespace Magestore\Rewardpoints\Model\ResourceModel\Rate\Spending;

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
        $this->addFieldToFilter('direction',\Magestore\Rewardpoints\Model\Rate::POINT_TO_MONEY);
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param string|int|array|null $condition
     * @return \Magestore\Rewardpoints\Model\ResourceModel\Rate\Spending\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {

        if ($field == 'customer_group_ids') {
            if(isset($condition['eq']) && $condition['eq']) {
                $condition = array('finset' => $condition['eq']);
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
