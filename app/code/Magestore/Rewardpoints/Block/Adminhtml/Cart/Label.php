<?php
namespace Magestore\Rewardpoints\Block\Adminhtml\Cart;
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

/**
 * RewardPoints Show Cart Total (Review about Earning/Spending Reward Points) on Backend
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Label extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    protected $_template = 'rewardpoints/checkout/cart/label.phtml';

    public $_objectManager;

    public function _construct()
    {
        parent::_construct();
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * check reward points system is enabled or not
     *
     * @return boolean
     */
    public function isEnable() {
        return $this->_objectManager->create('Magestore\Rewardpoints\Helper\Data')->isEnable();
    }

    /**
     * get reward points helper
     *
     * @return Magestore_RewardPoints_Helper_Point
     */
    public function getPointHelper()
    {
        return $this->_objectManager->create('Magestore\Rewardpoints\Helper\Point');
    }

    /**
     * get total points that customer use to spend for order
     *
     * @return int
     */
    public function getSpendingPoint()
    {
        return $this->_objectManager->create('Magestore\Rewardpoints\Helper\Calculation\Spending')->getTotalPointSpent();
    }

    /**
     * get total points that customer can earned by purchase order
     *
     * @return int
     */
    public function getEarningPoint()
    {
        return $this->_objectManager->create('Magestore\Rewardpoints\Helper\Calculation\Earning')->getTotalPointsEarning();
    }
}
