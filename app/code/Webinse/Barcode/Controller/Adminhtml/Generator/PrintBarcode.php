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
 * PrintBarcode action.
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */

namespace Webinse\Barcode\Controller\Adminhtml\Generator;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Request\Http;
use Webinse\Barcode\Model\Generator;

class PrintBarcode extends Action
{
    /** @var Product */
    protected $_modelProduct;

    /** @var FileFactory */
    protected $_fileFactory;

    /** @var Http */
    protected $_request;

    /** @var Generator */
    protected $_modelGenerator;

    /** @var $leftOffset */
    private $leftOffset;

    /** @var $topOffset */
    private $topOffset;

    /**
     * PrintBarcode constructor.
     * @param Action\Context $context
     * @param Product $modelProduct
     * @param FileFactory $fileFactory
     * @param Http $request
     * @param Generator $modelGenerator
     */
    public function __construct(
        Action\Context $context,
        Product $modelProduct,
        FileFactory $fileFactory,
        Http $request,
        Generator $modelGenerator
    ) {
        $this->_modelProduct = $modelProduct;
        $this->_fileFactory = $fileFactory;
        $this->_request = $request;
        $this->_modelGenerator = $modelGenerator;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->createPDF();
        return $resultRedirect->setPath('*/*/');
    }

    public function createPDF()
    {
        $pdf = new \Zend_Pdf();
        $pageFormat = \Zend_Pdf_Page::SIZE_A4;
        $page = $pdf->newPage($pageFormat);
        $pdf->pages[] = $page;

        if (!empty($this->_request->getParams()['product'])) {
            $productId = $this->_request->getParams()['product'];
            $product = $this->_modelProduct->load($productId);
            $model = $this->_modelGenerator->load($product->getSku(), "sku");
        } else {
            $model = $this->_modelGenerator->load($this->_request->getParams()['id']);
        }

        if (!empty($model->getData())) {
            $filename = 'var/tmp/barcode' . $model->getBarcode() . "." . $model->getImageFormat();
            file_put_contents($filename, base64_decode($model->getEncodedImage()));
            $image = \Zend_Pdf_Image::imageWithPath($filename);
            $this->leftOffset = 20;
            $this->topOffset = 20;
            $bottomPadding = 20;

            if (!empty($this->_request->getParams()['q'])){
                $quantity = (int) $this->_request->getParams()['q'];
            } else {
                $quantity = 10;
            }
            for ($i = 0; $i < $quantity; $i++) {
                $imageHeight = $image->getPixelHeight() * 72 / 96;
                $imageWidth = $image->getPixelWidth() * 72 / 96;
                if ($this->getPageSize($pageFormat)["width"] - $this->leftOffset - $imageWidth > 20) {
                    $this->drawImage($page, $image, $this->leftOffset, $this->topOffset, $pageFormat);
                } else if ($this->topOffset + $imageHeight + $bottomPadding > $this->getPageSize($pageFormat)["height"] - $imageHeight - 20) {
                    $page = $pdf->newPage($pageFormat);
                    $this->leftOffset = 20;
                    $this->topOffset = 20;
                    $this->drawImage($page, $image, $this->leftOffset, $this->topOffset, $pageFormat);
                    $pdf->pages[] = $page;
                } else {
                    $this->leftOffset = 20;
                    $this->topOffset += $imageHeight + $bottomPadding;
                    $this->drawImage($page, $image, $this->leftOffset, $this->topOffset, $pageFormat);
                }
            }

            $pdfData = $pdf->render();
            unlink($filename);

            return $this->_fileFactory->create(
                'barcode_' . $model->getBarcode() . '.pdf',
                $pdfData,
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } else {
            $this->messageManager->addError("The barcode cannot be printed because it does not exist. Please generate it.");
            return true;
        }
    }

    public function drawImage(\Zend_Pdf_Page $page, \Zend_Pdf_Resource_Image $image, $leftOffset, $topOffset, $pageFormat)
    {
        $page->drawImage(
            $image,
            $leftOffset,
            $this->getTopCorner($topOffset, $pageFormat) - ($image->getPixelHeight() * 72 / 96),
            ($image->getPixelWidth() * 72 / 96) + $leftOffset,
            $this->getTopCorner($topOffset, $pageFormat)
        );
        $rightPadding = 35;
        $this->leftOffset += ($image->getPixelWidth() * 72 / 96) + $rightPadding;
    }

    public function getTopCorner($height, $pageFormat)
    {
        return $this->getPageSize($pageFormat)["height"] - $height;
    }

    public function getPageSize($pageFormat)
    {
        $size = explode(':', $pageFormat);
        return ["width" => $size[0], "height" => $size[1]];
    }
}