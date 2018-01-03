<?php
namespace Webinse\Barcode\Controller\Adminhtml\Generator;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Session;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Webinse\Barcode\lib\Picqer\Barcode\BarcodeGenerator;
use Webinse\Barcode\lib\Picqer\Barcode\BarcodeGeneratorJPG;
use Webinse\Barcode\lib\Picqer\Barcode\BarcodeGeneratorPNG;
use Webinse\Barcode\lib\Picqer\Barcode\BarcodeGeneratorSVG;
use Webinse\Barcode\Model\Generator;
use Webinse\Barcode\Model\History;
use Webinse\Barcode\Model\Config;

class Save extends Action
{
    /** @var $_modelSession */
    protected $_modelSession;

    /** @var $_authSession */
    protected $_authSession;

    /** @var $_barcodeGeneratorJPG */
    protected $_barcodeGenerator;

    /** @var BarcodeGeneratorJPG */
    protected $_barcodeGeneratorJPG;

    /** @var BarcodeGeneratorJPG */
    protected $_barcodeGeneratorPNG;

    /** @var BarcodeGeneratorJPG */
    protected $_barcodeGeneratorSVG;

    /** @var $_modelGenerator */
    protected $_modelGenerator;

    /** @var $_modelHistory */
    protected $_modelHistory;

    /** @var Config */
    protected $_config;

    /**
     * Save constructor.
     * @param Session $modelSession
     * @param AuthSession $authSession
     * @param BarcodeGeneratorJPG $barcodeGeneratorJPG
     * @param BarcodeGeneratorPNG $barcodeGeneratorPNG
     * @param BarcodeGeneratorSVG $barcodeGeneratorSVG
     * @param Generator $modelGenerator
     * @param History $modelHistory
     * @param Config $config
     * @param Action\Context $context
     */
    public function __construct(
        Session $modelSession,
        AuthSession $authSession,
        BarcodeGeneratorJPG $barcodeGeneratorJPG,
        BarcodeGeneratorPNG $barcodeGeneratorPNG,
        BarcodeGeneratorSVG $barcodeGeneratorSVG,
        Generator $modelGenerator,
        History $modelHistory,
        Config $config,
        Action\Context $context
    ) {
        $this->_modelSession = $modelSession;
        $this->_authSession = $authSession;
        $this->_barcodeGeneratorJPG = $barcodeGeneratorJPG;
        $this->_barcodeGeneratorPNG = $barcodeGeneratorPNG;
        $this->_barcodeGeneratorSVG = $barcodeGeneratorSVG;
        $this->_modelGenerator = $modelGenerator;
        $this->_modelHistory = $modelHistory;
        $this->_config = $config;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_modelGenerator;

            $id = $this->getRequest()->getParam('entity_id');
            if ($id) {
                $model->load($id);
            } else {
                unset($data['entity_id']);
            }

            switch ($data["image_format"]) {
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

            $config = $this->getBarcodeConfig();
            $data["encoded_image"] = base64_encode($generator->getBarcode(
                $data['barcode'],
                $data['barcode_type'],
                $config['barcode_height_size'],
                $config['foreground_color'],
                $config['background_color']
            ));
            $model->setData($data);

            $this->_eventManager->dispatch(
                'webinse_barcode_generator_prepare_save',
                ['generator' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();

                $modelHistory = $this->_modelHistory;
                $authSession = $this->_authSession;
                $data["date"] = date("Y-m-d H:i:s");
                $data["username"] = $authSession->getUser()->getUsername();
                $modelHistory->setData($data);
                try {
                    $modelHistory->save();
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the barcode.'));
                }

                if (!$id) {
                    $id = $model->getCollection()->getLastItem()->getId();
                }
                $successUrl = $this->getUrl("webinse_barcode/generator/view", ['id' => $id]);
                $this->messageManager->addSuccess(__("Barcode with " . $data['barcode'] . " number have been successfully saved. You can check it <a href='" . $successUrl . "'>here</a>."));
                $this->_modelSession->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the barcode.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function getBarcodeConfig()
    {
        $config['barcode_height_size'] = $this->_config->getBarcodeHeightSize();
        $config['foreground_color'] = $this->_config->getForegroundColor() ?
            $this->rgbFormat($this->_config->getForegroundColor()) :
            [0, 0, 0];
        $config['background_color'] = $this->_config->getBackgroundColor() ?
            $this->rgbFormat($this->_config->getBackgroundColor()) :
            [255, 255, 255];

        return $config;
    }

    protected function rgbFormat($color)
    {
        list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
        return [$r, $g, $b];
    }
}