<?php
namespace Webinse\Barcode\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Magento\Ui\Component\Form\Fieldset as BaseFieldset;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;

class Fieldset extends BaseFieldset
{
    /** @var FieldFactory */
    private $fieldFactory;

    /** @var Product */
    protected $_modelProduct;

    /** @var StoreManagerInterface */
    protected $_storeManager;

    /** @var Http */
    protected $_request;

    /**
     * Fieldset constructor.
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     * @param FieldFactory $fieldFactory
     * @param Product $modelProduct
     * @param StoreManagerInterface $storeManager
     * @param Http $request
     */
    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = [],
        FieldFactory $fieldFactory,
        Product $modelProduct,
        StoreManagerInterface $storeManager,
        Http $request
    ) {
        parent::__construct($context, $components, $data);
        $this->fieldFactory = $fieldFactory;
        $this->_modelProduct = $modelProduct;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
    }

    /**
     * Get components
     *
     * @return UiComponentInterface[]
     */
    public function getChildComponents()
    {
        $id = $this->_request->getParams()['id'];
        $sku = $this->getContext()->getDataProvider()->getData()[$id]['sku'];
        $modelProduct = $this->_modelProduct;
        $idProduct = $modelProduct->getIdBySku($sku);
        if ($idProduct) {
            $product = $modelProduct->load($idProduct);
            $catalogProductImageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "catalog/product";
            $currency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        } else {
            $product = false;
        }

        if ($product) {
            $fields = [
                [
                    'label' => __('ID'),
                    'value' => __($product->getId()),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
                [
                    'label' => __('Name'),
                    'value' => __($product->getName()),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
                [
                    'label' => __('SKU'),
                    'value' => __($product->getSku()),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
                [
                    'label' => __('Image'),
                    'value' => __($catalogProductImageUrl . $product->getImage()),
                    'formElement' => 'input',
                    'component' => 'Webinse_Barcode/js/form/element/product-image',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/product-image'
                ],
                [
                    'label' => __('Description'),
                    'value' => __($product->getDescription()),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/product-description'
                ],
                [
                    'label' => __('Price'),
                    'value' => __(strripos((float)$product->getPrice(), ".")
                        ? (float)$product->getPrice() . " " . $currency
                        : (float)$product->getPrice() . ".00 " . $currency),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
                [
                    'label' => __('Is in stock'),
                    'value' => __($product->getQuantityAndStockStatus('is_in_stock') ? "Yes" : "No"),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
                [
                    'label' => __('Quantity'),
                    'value' => __($product->getQuantityAndStockStatus('qty')),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
            ];
        } else {
            $fields = [
                [
                    'value' => __("No product is associated with this barcode."),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
            ];
        }

        foreach ($fields as $k => $fieldConfig) {
            $fieldInstance = $this->fieldFactory->create();
            $name = 'my_dynamic_field_' . $k;

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
}