<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magecomp\Gstcharge\Block\Adminhtml\Sales\Order\View;

use Magento\Sales\Model\ResourceModel\Order\Item\Collection;
use Magento\Sales\Block\Adminhtml\Order\View\Items as OrderItems;
use Magento\Framework\Exception\LocalizedException;
/**
 * Adminhtml order items grid
 */
class Items extends OrderItems
{
    /**
     * @return array
     */
	 
    public function getColumns()
    {
		
        $columns = array_key_exists('columns', $this->_data) ? $this->_data['columns'] : [];
        return $columns;
    }

    /**
     * Retrieve required options from parent
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new LocalizedException(__('Invalid parent block for this block'));
        }
        $this->setOrder($this->getParentBlock()->getOrder());
        parent::_beforeToHtml();
    }

    /**
     * Retrieve order items collection
     *
     * @return Collection
     */
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }
}
