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
 * @module      RewardPoints
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

namespace Magestore\Rewardpoints\Model\ResourceModel\Customer;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     * @var string
     */
    protected $_idFieldName = 'reward_id';

    /**
     * Initialize collection resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Rewardpoints\Model\Customer', 'Magestore\Rewardpoints\Model\ResourceModel\Customer');
    }

}
