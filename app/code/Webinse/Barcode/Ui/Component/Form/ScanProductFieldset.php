<?php
namespace Webinse\Barcode\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Magento\Ui\Component\Form\Fieldset as BaseFieldset;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;
use Webinse\Barcode\Model\Generator;

class ScanProductFieldset extends BaseFieldset
{
    /** @var FieldFactory */
    private $fieldFactory;

    /** @var Product */
    protected $_modelProduct;

    /** @var StoreManagerInterface */
    protected $_storeManager;

    /** @var Http */
    protected $_request;

    /** @var Generator */
    protected $_modelGenerator;

    /**
     * ScanFieldset constructor.
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     * @param FieldFactory $fieldFactory
     * @param Product $modelProduct
     * @param StoreManagerInterface $storeManager
     * @param Http $request
     * @param Generator $modelGenerator
     */
    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = [],
        FieldFactory $fieldFactory,
        Product $modelProduct,
        StoreManagerInterface $storeManager,
        Http $request,
        Generator $modelGenerator
    ) {
        parent::__construct($context, $components, $data);
        $this->fieldFactory = $fieldFactory;
        $this->_modelProduct = $modelProduct;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_modelGenerator = $modelGenerator;
    }

    /**
     * Get components
     *
     * @return UiComponentInterface[]
     */
    public function getChildComponents()
    {
        if ($barcode = isset($this->_request->getParams()['barcode'])) {
            $barcode = $this->_request->getParams()['barcode'];
        }

        $modelGenerator = $this->_modelGenerator;
        if ($barcode) {
            $barcodeInfo = $modelGenerator->load($barcode, "barcode")->getData();
            $modelProduct = $this->_modelProduct;

            if ($productInfo = isset($barcodeInfo["sku"])) {
                if ($idProduct = $modelProduct->getIdBySku($barcodeInfo["sku"])) {
                    $productInfo = $modelProduct->load($idProduct);
                } else {
                    $productInfo = false;
                }
            }

            if ($productInfo) {
                $catalogProductImageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "catalog/product";
                $currency = $this->_storeManager->getStore()->getCurrentCurrencyCode();

                $fields = [
                    [
                        'label' => __('ID'),
                        'value' => __($productInfo->getId()),
                        'formElement' => 'input',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                    ],
                    [
                        'label' => __('Name'),
                        'value' => __($productInfo->getName()),
                        'formElement' => 'input',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                    ],
                    [
                        'label' => __('SKU'),
                        'value' => __($productInfo->getSku()),
                        'formElement' => 'input',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                    ],
                    [
                        'label' => __('Image'),
                        'value' => __($catalogProductImageUrl . $productInfo->getImage()),
                        'formElement' => 'input',
                        'component' => 'Webinse_Barcode/js/form/element/product-image',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'Webinse_Barcode/form/element/product-image'
                    ],
                    [
                        'label' => __('Description'),
                        'value' => __($productInfo->getDescription()),
                        'formElement' => 'input',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'Webinse_Barcode/form/element/product-description'
                    ],
                    [
                        'label' => __('Price'),
                        'value' => __(strripos((float)$productInfo->getPrice(), ".")
                            ? (float)$productInfo->getPrice() . " " . $currency
                            : (float)$productInfo->getPrice() . ".00 " . $currency),
                        'formElement' => 'input',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                    ],
                    [
                        'label' => __('Is in stock'),
                        'value' => __($productInfo->getQuantityAndStockStatus('is_in_stock') ? "Yes" : "No"),
                        'formElement' => 'input',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                    ],
                    [
                        'label' => __('Quantity'),
                        'value' => __($productInfo->getQuantityAndStockStatus('qty')),
                        'formElement' => 'input',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                    ],
                ];
            } else {
                $fields = [ $this->_showNoProduct("No product is associated with this barcode.") ];
            }
        } else {
            $fields = [ $this->_showNoProduct("No product is associated with this barcode.") ];
        }

        foreach ($fields as $k => $fieldConfig) {
            $fieldInstance = $this->fieldFactory->create();
            $name = 'scan_product_info_field_' . $k;

            $fieldInstance->setData(
                [
                    'config' => $fieldConfig,
                    'name' => $name
                ]
            );

            $fieldInstance->prepare();
            $this->addComponent($name, $fieldInstance);
        }

        return parent::getChildComponents();
    }

    protected function _showNoProduct($value)
    {
        return [
            'value' => __($value),
            'formElement' => 'input',
            'template' => 'ui/form/field',
            'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
        ];
    }
}