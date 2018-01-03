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
 * Model gets config from the database
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */

namespace Webinse\Barcode\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    protected $storeId = null;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isBarcodeModuleEnable()
    {
        return $this->config('general/enable');
    }

    public function getDefBarcodeValue()
    {
        return $this->config('barcode_config/barcode_value');
    }

    public function getBarcodePattern()
    {
        return $this->config('barcode_config/barcode_value_pattern');
    }

    public function getDefBarcodeType()
    {
        return $this->config('barcode_config/barcode_type');
    }

    public function getDefImageFormat()
    {
        return $this->config('barcode_config/barcode_image_format');
    }

    public function getForegroundColor()
    {
        return $this->config('barcode_design/foreground_color');
    }

    public function getBackgroundColor()
    {
        return $this->config('barcode_design/background_color');
    }

    public function getBarcodeHeightSize()
    {
        return $this->config('barcode_design/barcode_height_size');
    }

    public function isEnableBarcodeInvoice()
    {
        return $this->config('barcode_invoice/enable_barcode_invoice');
    }

    public function getLabelWidth()
    {
        return $this->config('product_labels/label_width');
    }

    public function getLabelHeight()
    {
        return $this->config('product_labels/label_height');
    }

    public function getLabelBackgroundColor()
    {
        $bgColorHex = $this->config('product_labels/label_background_color');
        return list($r, $g, $b) = sscanf($bgColorHex, "#%02x%02x%02x");
    }

    public function getProductImageWidth()
    {
        return $this->config('product_labels/image_width');
    }

    public function getHorizontalSpace()
    {
        return $this->config('product_labels/text_horizontal_position');
    }

    public function getVerticalSpace()
    {
        return $this->config('product_labels/text_vertical_position');
    }

    public function isProductName()
    {
        return $this->config('product_labels/include_name');
    }

    public function isProductSku()
    {
        return $this->config('product_labels/include_sku');
    }

    public function isProductPrice()
    {
        return $this->config('product_labels/include_price');
    }

    protected function config($code)
    {
        return $this->scopeConfig->getValue("barcode/{$code}", ScopeInterface::SCOPE_STORE, $this->storeId);
    }
}