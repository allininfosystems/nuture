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
 * @author        Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */


namespace Magestore\Rewardpoints\Api\Data\Customer;


interface CustomerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get customers list.
     *
     * @api
     * @return \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface[]
     */
    public function getItems();

    /**
     * Set customers list.
     *
     * @api
     * @param \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}