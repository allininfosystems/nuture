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
namespace Magestore\Rewardpoints\Api;
interface CustomerRepositoryInterface
{
    /**
     * @param string $param
     * @return \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($param);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Rewardpoints\Api\Data\Customer\CustomerSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * create new reward customer
     * @param \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer
     * @return \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer
     */
    public function save(\Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer);

    /**
     * @param string $param
     * @param \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer
     * @return \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer
     */
    public function update($param, \Magestore\Rewardpoints\Api\Data\Customer\CustomerInterface $rewardCustomer);

}