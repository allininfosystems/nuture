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

namespace Magestore\Rewardpoints\Model\Total\Pdf;
/**
 * Rewardpoints Spend for Order by Point Model (use to print invoice PDF)
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Point extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    /**
     * Get Total amount from source
     *
     * @return float
     */
    public function getAmount()
    {
        return -$this->getSource()->getRewardpointsDiscount();
    }
}
