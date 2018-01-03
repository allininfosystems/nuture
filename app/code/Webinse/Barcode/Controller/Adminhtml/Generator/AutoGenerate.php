<?php
namespace Webinse\Barcode\Controller\Adminhtml\Generator;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\FilterBuilder;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Webinse\Barcode\Model\Generator;
use Webinse\Barcode\Model\Config;
use Webinse\Barcode\lib\Picqer\Barcode\BarcodeGenerator;
use Webinse\Barcode\lib\Picqer\Barcode\BarcodeGeneratorJPG;
use Webinse\Barcode\lib\Picqer\Barcode\BarcodeGeneratorPNG;
use Webinse\Barcode\lib\Picqer\Barcode\BarcodeGeneratorSVG;

class AutoGenerate extends Action
{
    /** @var ProductRepository */
    protected $_productRepository;

    /** @var SearchCriteriaInterface */
    protected $_searchCriteria;

    /** @var FilterGroup */
    protected $_filterGroup;

    /** @var FilterBuilder */
    protected $_filterBuilder;

    /** @var Status */
    protected $_productStatus;

    /** @var Visibility */
    protected $_productVisibility;

    /** @var Generator */
    protected $_modelGenerator;

    /** @var Config */
    protected $_modelConfig;

    /** @var BarcodeGeneratorJPG */
    protected $_barcodeGeneratorJPG;

    /** @var BarcodeGeneratorJPG */
    protected $_barcodeGeneratorPNG;

    /** @var BarcodeGeneratorJPG */
    protected $_barcodeGeneratorSVG;

    /**
     * AutoGenerate constructor.
     * @param Action\Context $context
     * @param ProductRepository $productRepository
     * @param SearchCriteriaInterface $criteria
     * @param FilterGroup $filterGroup
     * @param FilterBuilder $filterBuilder
     * @param Status $productStatus
     * @param Visibility $productVisibility
     * @param Generator $modelGenerator
     * @param Config $config
     * @param BarcodeGeneratorJPG $barcodeGeneratorJPG
     * @param BarcodeGeneratorPNG $barcodeGeneratorPNG
     * @param BarcodeGeneratorSVG $barcodeGeneratorSVG
     */
    public function __construct(
        Action\Context $context,
        ProductRepository $productRepository,
        SearchCriteriaInterface $criteria,
        FilterGroup $filterGroup,
        FilterBuilder $filterBuilder,
        Status $productStatus,
        Visibility $productVisibility,
        Generator $modelGenerator,
        Config $config,
        BarcodeGeneratorJPG $barcodeGeneratorJPG,
        BarcodeGeneratorPNG $barcodeGeneratorPNG,
        BarcodeGeneratorSVG $barcodeGeneratorSVG
    ) {
        $this->_productRepository = $productRepository;
        $this->_searchCriteria = $criteria;
        $this->_filterGroup = $filterGroup;
        $this->_filterBuilder = $filterBuilder;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        $this->_modelGenerator = $modelGenerator;
        $this->_modelConfig = $config;
        $this->_barcodeGeneratorJPG = $barcodeGeneratorJPG;
        $this->_barcodeGeneratorPNG = $barcodeGeneratorPNG;
        $this->_barcodeGeneratorSVG = $barcodeGeneratorSVG;
        parent::__construct($context);
    }

    public function execute()
    {
        $product = $this->getProductData();
        $model = $this->_modelGenerator;
        $config = $this->_modelConfig;
        $barcodeConfig = $this->getBarcodeConfig();

        $barcodeType = $config->getDefBarcodeType();
        $imageFormat = $config->getDefImageFormat();

        switch ($imageFormat) {
            case "jpeg":
                $generator = $this->_barcodeGeneratorJPG;
                break;
            case "png":
                $generator = $this->_barcodeGeneratorPNG;
                break;
            case "svg":
                $generator = $this->_barcodeGeneratorSVG;
                break;
            default:
                $generator = $this->_barcodeGeneratorJPG;
        }

        foreach ($product as $item) {
            if ($model->load($item->getSku(), "sku")->getSku() === $item->getSku()) {
                continue;
            }

            switch ($config->getDefBarcodeValue()) {
                case "random":
                    $barcodeValue = $this->_generateRandomBarcode();
                    break;
                case "pattern":
                    $barcodeValue = $this->_generateBarcodeByPattern();
                    break;
                case "sku":
                    $barcodeValue = $item->getSku();
                    break;
                case "name":
                    $barcodeValue = $item->getName();
                    break;
                case "price":
                    $barcodeValue = $item->getPrice();
                    break;
                default:
                    $barcodeValue = $this->_generateRandomBarcode();
            }

            $data["product_name"] = $item->getName();
            $data["sku"] = $item->getSku();
            $data["barcode"] = $barcodeValue;
            $data["barcode_type"] = $barcodeType;
            $data["image_format"] = $imageFormat;
            $data["encoded_image"] = base64_encode($generator->getBarcode(
                $data['barcode'],
                $data['barcode_type'],
                $barcodeConfig['barcode_height_size'],
                $barcodeConfig['foreground_color'],
                $barcodeConfig['background_color']
            ));
            $model->setData($data);
            try {
                $model->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the barcode.'));
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('webinse_barcode/generator/');
    }

    protected function getProductData()
    {

        $this->_filterGroup->setFilters([
            $this->_filterBuilder
                ->setField('status')
                ->setConditionType('in')
                ->setValue($this->_productStatus->getVisibleStatusIds())
                ->create(),
            $this->_filterBuilder
                ->setField('visibility')
                ->setConditionType('in')
                ->setValue($this->_productVisibility->getVisibleInSiteIds())
                ->create(),
        ]);

        $this->_searchCriteria->setFilterGroups([$this->_filterGroup]);
        $products = $this->_productRepository->getList($this->_searchCriteria);
        $productItems = $products->getItems();

        return $productItems;
    }

    protected function getBarcodeConfig()
    {
        $config['barcode_height_size'] = $this->_modelConfig->getBarcodeHeightSize();
        $config['foreground_color'] = $this->_modelConfig->getForegroundColor() ?
            $this->rgbFormat($this->_modelConfig->getForegroundColor()) :
            [0, 0, 0];
        $config['background_color'] = $this->_modelConfig->getBackgroundColor() ?
            $this->rgbFormat($this->_modelConfig->getBackgroundColor()) :
            [255, 255, 255];

        return $config;
    }

    protected function rgbFormat($color)
    {
        list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
        return [$r, $g, $b];
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