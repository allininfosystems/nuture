<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Block\Totals\Order;

/**
 * Rewardpoints Total Label Block
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Point extends \Magento\Sales\Block\Order\Totals
{

    /**
     * Point constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helperPoint = $helperPoint;
        parent::__construct($context,$registry,$data);
    }
    /**
     * add points label into order total
     *     
     */
    public function initTotals()
    {
        if (!$this->_helperPoint->getGeneralConfig('enable')) {
            return $this;
        }
        $totalsBlock = $this->getParentBlock();
        $order = $totalsBlock->getOrder();

        if ($order->getRewardpointsEarn()) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject(array(
                'code'  => 'rewardpoints_earn_label',
                'label' => __('Earn Points'),
                'value' => $this->_helperPoint->format($order->getRewardpointsEarn()),
                'is_formated'   => true,
            )),'subtotal');
        }

        if ($order->getRewardpointsSpent()) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject(array(
                'code'  => 'rewardpoints_spent_label',
                'label' => __('Spend Points'),
                'value' => $this->_helperPoint->format($order->getRewardpointsSpent()),
                'is_formated'   => true,
            )), 'rewardpoints_earn_label');
        }

        if ($order->getRewardpointsDiscount()>=0.0001) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject(array(
                'code'  => 'rewardpoints',
                'label' => __('Use points on spend'),
                'value' => -$order->getRewardpointsDiscount(),
                'base_value' => -$order->getRewardpointsBaseDiscount(),
            )), 'rewardpoints_spent_label');
        }
    }
}
