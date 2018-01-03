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


interface TransactionRepositoryInterface
{
    /**
     * @param string $param
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($param);


    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $id
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     */
    public function complete($id);


    /**
     * @param string $id
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     */
    public function cancel($id);

    /**
     * @param string $id
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     */
    public function expire($id);

    /**
     * @param \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface $transaction
     * @return \Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface
     */
    public function save(\Magestore\Rewardpoints\Api\Data\Transaction\TransactionInterface $transaction);

}