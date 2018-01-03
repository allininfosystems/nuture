<?php
namespace Webinse\Barcode\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Magento\Ui\Component\Form\Fieldset as BaseFieldset;
use Magento\Framework\App\Request\Http;
use Webinse\Barcode\Model\Generator;

class ScanBarcodeFieldset extends BaseFieldset
{
    /** @var FieldFactory */
    private $fieldFactory;

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
     * @param Http $request
     * @param Generator $modelGenerator
     */
    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = [],
        FieldFactory $fieldFactory,
        Http $request,
        Generator $modelGenerator
    ) {
        parent::__construct($context, $components, $data);
        $this->fieldFactory = $fieldFactory;
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
        $barcodeInfo = $modelGenerator->load($barcode, "barcode")->getData();
        if (!empty($barcodeInfo)) {
            $fields = [
                [
                    'label' => __('Barcode'),
                    'value' => __($barcodeInfo["encoded_image"]),
                    'formElement' => 'input',
                    'component' => 'Webinse_Barcode/js/form/element/barcode',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/barcode'
                ],
                [
                    'label' => __('Barcode number'),
                    'value' => __($barcodeInfo["barcode"]),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
                [
                    'label' => __('Barcode type'),
                    'value' => __($barcodeInfo["barcode_type"]),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
                [
                    'label' => __('Barcode image format'),
                    'value' => __($barcodeInfo["image_format"]),
                    'formElement' => 'input',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'Webinse_Barcode/form/element/field-text'
                ],
            ];
        } else {
            $fields = [ $this->_showNoProduct("A barcode was not found.") ];
        }

        foreach ($fields as $k => $fieldConfig) {
            $fieldInstance = $this->fieldFactory->create();
            $name = 'scan_barcode_info_field_' . $k;

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