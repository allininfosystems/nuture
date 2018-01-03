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
/**
 * Interface CustomerInterface
 * @package Magestore\Rewardpoints\Api\Data\Customer
 */
interface CustomerInterface
{
    const REWARD_ID           = 'reward_id';
    const CUSTOMER_ID         = 'customer_id';
    const POINT_BALANCE       = 'point_balance';
    const HOLDING_BALANCE     = 'holding_balance';
    const SPENT_BALANCE       = 'spent_balance';
    const IS_NOTIFICATION     = 'is_notification';
    const EXPIRE_NOTIFICATION = 'expire_notification';

    /**
     * @return string
     */
    public function getRewardId();

    /**
     * @param $id
     * @return mixed
     */
    public function setRewardId($id);

    /**
     * @return mixed
     */
    public function getCustomerId();

    /**
     * @param $id
     * @return mixed
     */
    public function setCustomerId($id);

    /**
     * @return mixed
     */
    public function getPointBalance();

    /**
     * @param $balance
     * @return mixed
     */
    public function setPointBalance($balance);

    /**
     * @return mixed
     */
    public function getHoldingBalance();

    /**
     * @param $balance
     * @return mixed
     */
    public function setHoldingBalance($balance);

    /**
     * @return mixed
     */
    public function getSpentBalance();

    /**
     * @param $balance
     * @return mixed
     */
    public function setSpentBalance($balance);
     /**
     * @return mixed
     */
    public function getEmail();

    /**
     * @param $email
     * @return mixed
     */
    public function setEmail($email);

//    /**
//     * @return mixed
//     */
//    public function getIsNotification();
//
//    /**
//     * @param $isNotification
//     * @return mixed
//     */
//    public function setIsNotification($isNotification);
//
//    /**
//     * @return mixed
//     */
//    public function getExpireNotification();
//
//    /**
//     * @param $expireNotification
//     * @return mixed
//     */
//    public function setExpireNotification($expireNotification);


}
