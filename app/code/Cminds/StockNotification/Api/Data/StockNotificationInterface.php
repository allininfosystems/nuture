<?php

namespace Cminds\StockNotification\Api\Data;

interface StockNotificationInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const PRODUCT_ID = 'product_id';
    const CUSTOMER_ID = 'customer_id';
    const EMAIL = 'email';
    const NOTIFIED = 'notified';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id
     *
     * @return StockNotificationInterface
     */
    public function setId($id);

    /**
     * Get product id
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product id
     *
     * @param int $productId
     *
     * @return StockNotificationInterface
     */
    public function setProductId($productId);

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId
     *
     * @return StockNotificationInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     *
     * @return StockNotificationInterface
     */
    public function setEmail($email);

    /**
     * Get notified flag
     *
     * @return bool
     */
    public function getNotified();

    /**
     * Set notified flag
     *
     * @param bool $flag
     *
     * @return StockNotificationInterface
     */
    public function setNotified($flag);

    /**
     * Get update date
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set update date
     *
     * @param string $updateDate
     *
     * @return StockNotificationInterface
     */
    public function setUpdatedAt($updateDate);

    /**
     * Get create date
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set create date
     *
     * @param string $createdDate
     *
     * @return StockNotificationInterface
     */
    public function setCreatedAt($createdDate);
}
