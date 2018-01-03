<?php

namespace Cminds\StockNotification\Model;

use Cminds\StockNotification\Api\Data\StockNotificationInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class StockNotification extends AbstractModel implements IdentityInterface, StockNotificationInterface
{
    /**
     * Cache tag
     *
     * @const string
     */
    const CACHE_TAG = 'cminds_stocknotification_request';

    protected $_eventPrefix = 'cminds_stocknotification_request';
    protected $_cacheTag = 'cminds_stocknotification_request';

    /**
     * StockNotification Model initialization
     */
    protected function _construct()
    {
        $this->_init(\Cminds\StockNotification\Model\ResourceModel\StockNotification::class);
    }

    /**
     * Return unique ID(s) for each object in system.
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get product id.
     *
     * @return int|null
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Set product id.
     *
     * @param int $productId
     *
     * @return StockNotification
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get customer id.
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer id.
     *
     * @param int $customerId
     *
     * @return StockNotification
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return StockNotification
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get notified flag
     *
     * @return bool
     */
    public function getNotified()
    {
        return $this->getData(self::NOTIFIED);
    }

    /**
     * Set notified flag.
     *
     * @param bool $flag
     *
     * @return StockNotification
     */
    public function setNotified($flag)
    {
        return $this->setData(self::NOTIFIED, $flag);
    }

    /**
     * Get update date.
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set update date.
     *
     * @param string $updateDate
     *
     * @return StockNotification
     */
    public function setUpdatedAt($updateDate)
    {
        return $this->setData(self::UPDATED_AT, $updateDate);
    }

    /**
     * Get create date.
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set create date.
     *
     * @param string $createdDate
     *
     * @return StockNotification
     */
    public function setCreatedAt($createdDate)
    {
        return $this->setData(self::CREATED_AT, $createdDate);
    }
}
