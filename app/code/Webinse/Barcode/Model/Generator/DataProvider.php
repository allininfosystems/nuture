<?php
namespace Webinse\Barcode\Model\Generator;

use Magento\Catalog\Model\Product;
use Webinse\Barcode\Model\ResourceModel\Generator\CollectionFactory;
use Webinse\Barcode\Controller\Adminhtml\Generator\Generate;
use Webinse\Barcode\Model\Config;
use Webinse\Barcode\Model\Generator;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var Product */
    protected $_product;

    /** @var $_loadedData */
    protected $_loadedData;

    /** @var Generate */
    protected $_generatorController;

    /** @var Config */
    protected $_modelConfig;

    /** @var Generator */
    protected $_modelGenerator;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Product $product
     * @param CollectionFactory $barcodeCollectionFactory
     * @param Generate $generatorController
     * @param Config $modelConfig
     * @param Generator $modelGenerator
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Product $product,
        CollectionFactory $barcodeCollectionFactory,
        Generate $generatorController,
        Config $modelConfig,
        Generator $modelGenerator,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $barcodeCollectionFactory->create();
        $this->_product = $product;
        $this->_generatorController = $generatorController;
        $this->_modelConfig = $modelConfig;
        $this->_modelGenerator = $modelGenerator;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $productId = $this->_generatorController->getProductId();
        $modelConfig = $this->_modelConfig;
        $product = $this->_product->load($productId);
        if ($productId) {
            switch ($this->_modelConfig->getDefBarcodeValue()) {
                case "random":
                    $barcodeValue = $this->_generateRandomBarcode();
                    break;
                case "pattern":
                    $barcodeValue = $this->_generateBarcodeByPattern();
                    break;
                case "sku":
                    $barcodeValue = $product->getSku();
                    break;
                case "name":
                    $barcodeValue = $product->getName();
                    break;
                case "price":
                    $barcodeValue = $product->getPrice();
                    break;
                default:
                    $barcodeValue = $this->_generateRandomBarcode();
            }

            $this->_loadedData[$productId]['product_name'] = $product->getName();
            $this->_loadedData[$productId]['sku'] = $product->getSku();
            $this->_loadedData[$productId]['barcode'] = $barcodeValue;
            $this->_loadedData[$productId]['barcode_type'] = $modelConfig->getDefBarcodeType();
            $this->_loadedData[$productId]['image_format'] = $modelConfig->getDefImageFormat();
        } else {
            if (isset($this->_loadedData)) {
                return $this->_loadedData;
            }
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $this->_loadedData[$item->getId()] = $item->getData();
            }
        }
        return $this->_loadedData;
    }

    protected function _generateRandomBarcode()
    {
        $number = rand(0, 999999999999);
        return $rand[] = str_pad($number, 12, "0", STR_PAD_LEFT);
    }

    protected function _generateBarcodeByPattern() {
        $pattern = $this->_modelConfig->getBarcodePattern();
        $parts = explode("/", $pattern);
        $arrayVariableParts = explode(",", $parts[1]);
        $variablePart = "";
        foreach ($arrayVariableParts as $part) {
            $part = explode(":", $part);
            switch ($part[0]) {
                case "w":
                    $variablePart .= $this->_getRandomLetters($part[1]);
                    break;
                case "d":
                    $variablePart .= $this->_getRandomDigits($part[1]);
                    break;
                case "wd":
                    $variablePart .= $this->_getRandomLettersAndDigits($part[1]);
                    break;
            }
        }
        return $parts[0] . $variablePart;
    }

    private function _getRandomDigits($quantity)
    {
        $result = "";
        for ($i = 0; $i < $quantity; $i++) {
            $result .= rand(0, 9);
        }
        return $result;
    }

    private function _getRandomLetters($quantity)
    {
        $result = "";
        for ($i = 0; $i < $quantity; $i++) {
            $result .= chr(rand(65,90));
        }
        return $result;
    }

    private function _getRandomLettersAndDigits($quantity)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $result = "";
        for ($i = 0; $i < $quantity; $i++) {
            $result .= $characters[rand(0, $charactersLength - 1)];
        }
        return $result;
    }
}
