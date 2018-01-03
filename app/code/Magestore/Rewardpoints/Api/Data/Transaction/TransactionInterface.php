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

namespace Magestore\Rewardpoints\Api\Data\Transaction;


interface TransactionInterface
{
    /**
     * @return mixed
     */
    public function getTransactionId();

    /**
     * @return mixed
     */
    public function getRewardId();

    /**
     * @return mixed
     */
    public function getCustomerId();

    /**
     * @return mixed
     */
    public function getCustomerEmail();

    /**
     * @return mixed
     */
    public function getCurrentPointBalance();

    /**
     * @return mixed
     */
    public function getTitle();

    /**
     * @return mixed
     */
    public function getAction();

    /**
     * @return mixed
     */
    public function getStoreId();

    /**
     * @return mixed
     */
    public function getPointAmount();

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @return mixed
     */
    public function getStatusLabel();

    /**
     * @return mixed
     */
    public function getCreatedTime();

    /**
     * @return mixed
     */
    public function getUpdatedTime();

    /**
     * @return mixed
     */
    public function getExpirationDate();

    /**
     * @return mixed
     */
    public function getOrderId();

    /**
     * @return mixed
     */
    public function getOrderIncrementId();

    /**
     * @return mixed
     */
    public function getOrderAmount();

    /**
     * @return mixed
     */
    public function getDiscount();

    /**
     * @return mixed
     */
    public function getExtraContent();

    /**
     * @param $transactionId
     * @return mixed
     */
    public function setTransactionId($transactionId);

    /**
     * @param $rewardId
     * @return mixed
     */
    public function setRewardId($rewardId);

    /**
     * @param $customerId
     * @return mixed
     */
    public function setCustomerId($customerId);

    /**
     * @param $customerEmail
     * @return mixed
     */
    public function setCustomerEmail($customerEmail);

    /**
     * @param $balance
     * @return mixed
     */
    public function setCurrentPointBalance($balance);

    /**
     * @param $title
     * @return mixed
     */
    public function setTitle($title);

    /**
     * @param $action
     * @return mixed
     */
    public function setAction($action);

    /**
     * @param $storeId
     * @return mixed
     */
    public function setStoreId($storeId);

    /**
     * @param $pointAmount
     * @return mixed
     */
    public function setPointAmount($pointAmount);

    /**
     * @param $status
     * @return mixed
     */
    public function setStatus($status);

    /**
     * @param $createdTime
     * @return mixed
     */
    public function setCreatedTime($createdTime);

    /**
     * @param $time
     * @return mixed
     */
    public function setUpdatedTime($time);

    /**
     * @param $time
     * @return mixed
     */
    public function setExpirationDate($time);

    /**
     * @param $id
     * @return mixed
     */
    public function setOrderId($id);

    /**
     * @param $id
     * @return mixed
     */
    public function setOrderIncrementId($id);

    /**
     * @param $amount
     * @return mixed
     */
    public function setOrderAmount($amount);

    /**
     * @param $discount
     * @return mixed
     */
    public function setDiscount($discount);

    /**
     * @param $content
     * @return mixed
     */
    public function setExtraContent($content);


}