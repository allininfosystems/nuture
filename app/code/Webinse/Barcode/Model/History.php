<?php
/**
 * Webinse
 *
 * PHP Version 5.6.23
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */
/**
 * Model for the History of created barcodes.
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */

namespace Webinse\Barcode\Model;

class History extends \Magento\Framework\Model\AbstractModel implements
    \Webinse\Barcode\Api\Data\HistoryInterface,
    \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'webinse_barcode_history';

    protected $_cacheTag = 'webinse_barcode_history';

    protected $_eventPrefix = 'webinse_barcode_history';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webinse\Barcode\Model\ResourceModel\History');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    public function getBarcode()
    {
        return $this->getData(self::BARCODE);
    }

    public function getBarcodeType()
    {
        return $this->getData(self::BARCODE_TYPE);
    }

    public function getImageFormat()
    {
        return $this->getData(self::IMAGE_FORMAT);
    }

    public function getEncodedImage()
    {
        return $this->getData(self::ENCODED_IMAGE);
    }

    public function getUsername()
    {
        return $this->getData(self::USERNAME);
    }

    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function setProductName($productName)
    {
        return $this->setData(self::PRODUCT_NAME, $productName);
    }

    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    public function setBarcode($barcode)
    {
        return $this->setData(self::BARCODE, $barcode);
    }

    public function setBarcodeType($barcodeType)
    {
        return $this->setData(self::BARCODE_TYPE, $barcodeType);
    }

    public function setImageFormat($imageFormat)
    {
        return $this->setData(self::IMAGE_FORMAT, $imageFormat);
    }

    public function setEncodedImage($encoded_image)
    {
        return $this->setData(self::ENCODED_IMAGE, $encoded_image);
    }

    public function setUsername($username)
    {
        return $this->setData(self::USERNAME, $username);
    }

    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }
}