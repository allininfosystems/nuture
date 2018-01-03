<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction;
use Magento\Framework\Data\OptionSourceInterface;
/**
 * Class Options
 */
class StoreView implements OptionSourceInterface
{
    protected $_array;
    /**
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storesFactory
     */
    public function __construct(\Magento\Store\Model\ResourceModel\Store\CollectionFactory $storesFactory)
    {
        $this->_storesFactory = $storesFactory;
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        if (!$this->_array) {
            /** @var $stores \Magento\Store\Model\ResourceModel\Store\Collection */
            $stores = $this->_storesFactory->create();
            $this->_array = $stores->load()->toOptionHash();
        }
        return $this->_array;
    }
    public function toOptionArray()
    {
        $options = array();
        foreach (self::toOptionHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }
}
