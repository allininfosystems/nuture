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
 * Data Interface for the History of created barcodes.
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */

namespace Webinse\Barcode\Api\Data;

interface HistoryInterface
{
    const ID        = 'entity_id';
    const PRODUCT_NAME = 'product_name';
    const SKU       = 'sku';
    const BARCODE   = 'barcode';
    const BARCODE_TYPE = 'barcode_type';
    const IMAGE_FORMAT = 'image_format';
    const ENCODED_IMAGE = 'encoded_image';
    const USERNAME = 'username';
    const DATE = 'date';

    /**
     * Get entity id.
     *
     * @return int
     */
    public function getId();

    /**
     * Get product name.
     *
     * @return string
     */
    public function getProductName();

    /**
     * Get product sku.
     *
     * @return string
     */
    public function getSku();

    /**
     * Get barcode value.
     *
     * @return string
     */
    public function getBarcode();

    /**
     * Get barcode type.
     *
     * @return string
     */
    public function getBarcodeType();

    /**
     * Get image format.
     *
     * @return string
     */
    public function getImageFormat();

    /**
     * Get barcode encoded image.
     *
     * @return string
     */
    public function getEncodedImage();

    /**
     * Get Username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get date.
     *
     * @return string
     */
    public function getDate();

    /**
     * Set entity id.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set product name.
     *
     * @param int $productName
     * @return $this
     */
    public function setProductName($productName);

    /**
     * Set sku from product.
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Set barcode value.
     *
     * @param string $barcode
     * @return $this
     */
    public function setBarcode($barcode);

    /**
     * Set barcode type.
     *
     * @param string $barcodeType
     * @return $this
     */
    public function setBarcodeType($barcodeType);

    /**
     * Set image format.
     *
     * @param int $imageFormat
     * @return $this
     */
    public function setImageFormat($imageFormat);

    /**
     * Set encoded image
     *
     * @param string $encoded_image
     * @return $this
     */
    public function setEncodedImage($encoded_image);

    /**
     * Set Username
     *
     * @param string $username
     * @return $this
     */
    public function setUsername($username);

    /**
     * Set date.
     *
     * @param string $date
     * @return $this
     */
    public function setDate($date);
}