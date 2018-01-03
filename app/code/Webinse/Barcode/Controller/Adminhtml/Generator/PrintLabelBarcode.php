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
 * Print Label Barcode action.
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
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir\Reader;
use Webinse\Barcode\Model\Generator;
use Webinse\Barcode\Model\Config;

class PrintLabelBarcode extends Action
{
    /** @var Product */
    protected $_modelProduct;

    /** @var StoreManagerInterface */
    protected $_storeManager;

    /** @var FileFactory */
    protected $_fileFactory;

    /** @var Generator */
    protected $_modelGenerator;

    /** @var Reader */
    protected $_moduleReader;

    /** @var Config */
    protected $_modelConfig;

    /** @var $leftOffset */
    private $leftOffset;

    /** @var $topOffset */
    private $topOffset;

    /**
     * PrintLabelBarcode constructor.
     * @param Action\Context $context
     * @param Product $modelProduct
     * @param StoreManagerInterface $storeManager
     * @param FileFactory $fileFactory
     * @param Reader $moduleReader
     * @param Generator $generator
     * @param Config $config
     */
    public function __construct(
        Action\Context $context,
        Product $modelProduct,
        StoreManagerInterface $storeManager,
        FileFactory $fileFactory,
        Reader $moduleReader,
        Generator $generator,
        Config $config
    ) {
        $this->_modelProduct = $modelProduct;
        $this->_storeManager = $storeManager;
        $this->_fileFactory = $fileFactory;
        $this->_moduleReader = $moduleReader;
        $this->_modelGenerator = $generator;
        $this->_modelConfig = $config;
        parent::__construct($context);
    }

    public function execute()
    {
        $label = $this->createLabel();

        $pdf = new \Zend_Pdf();
        $pageFormat = \Zend_Pdf_Page::SIZE_A4;
        $page = $pdf->newPage($pageFormat);
        $pdf->pages[] = $page;

        $filename = 'var/tmp/label.jpg';
        file_put_contents($filename, base64_decode($label));
        $image = \Zend_Pdf_Image::imageWithPath($filename);
        $this->leftOffset = 20;
        $this->topOffset = 20;
        $bottomPadding = 5;

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
            'label_with_barcode.pdf',
            $pdfData,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }

    public function createLabel()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $config = $this->_modelConfig;
        if (!function_exists('imagecreate')) {
            $this->messageManager->addError("Error! \"imagecreate\" function not found.");
            return $resultRedirect->setPath('*/*/');
        }
        //Label params
        $imageLabel = imagecreate($config->getLabelWidth(), $config->getLabelHeight());
        $configBgColor = $config->getLabelBackgroundColor();
        $colorBackground = imagecolorallocate($imageLabel, $configBgColor[0], $configBgColor[1], $configBgColor[2]);
        $black = imagecolorallocate($imageLabel, 0, 0, 0);
        imagecolortransparent($imageLabel, $colorBackground);
        imagerectangle ( $imageLabel, 0, 0, imagesx($imageLabel) - 1, imagesy($imageLabel) - 1, $black);

        //Product thumbnail and barcode
        if ($id = $this->getRequest()->getParam("id")) {
            $modelGenerator = $this->_modelGenerator->load($id);

            if ($idProduct = $this->_modelProduct->getIdBySku($modelGenerator->getSku(), "sku")) {
                $product = $this->_modelProduct->load($idProduct);
                if ($product->getData()) {
                    //Product image params
                    $catalogProductImageUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "catalog/product";
                    $imagePath = $catalogProductImageUrl . $product->getImage();
                    list($source_image_width, $source_image_height, $source_image_type) = getimagesize($imagePath);
                    $source_gd_image = imagecreatefromjpeg($imagePath);
                    $source_aspect_ratio = $source_image_width / $source_image_height;
                    //Creates product thumbnail
                    $thumbnail_image_width = $config->getProductImageWidth();
                    $thumbnail_image_height = (int) ($config->getProductImageWidth() / $source_aspect_ratio);
                    $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
                    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
                    imagefilter($thumbnail_gd_image, IMG_FILTER_GRAYSCALE);
                    imagecopymerge($imageLabel, $thumbnail_gd_image, 10, 10, 0, 0, $thumbnail_image_width, $thumbnail_image_height, 100);

                    //Product text
                    $viewDir = $this->_moduleReader->getModuleDir(
                        \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
                        'Webinse_Barcode'
                    );
                    $font = $viewDir . "/adminhtml/web/css/fonts/roboto.ttf";
                    $hspace = $config->getHorizontalSpace();
                    $vspace = $config->getVerticalSpace();
                    if ($config->isProductName()) {
                        $text = $product->getName();
                        $words = explode(" ", $text);
                        foreach ($words as $word) {
                            $box = imagettfbbox(15, 0, $font, $word . " ");
                            if ($hspace + $box[2] > $config->getLabelWidth()) {
                                $hspace = $config->getHorizontalSpace();
                                $vspace += 30;
                            }
                            imagettftext($imageLabel, 15, 0, $hspace, $vspace, $black, $font, $word . " ");
                            $hspace += $box[2];
                        }
                        $vspace += 30;
                        $hspace = $config->getHorizontalSpace();
                    }
                    if ($config->isProductSku()) {
                        imagettftext($imageLabel, 15, 0, $hspace, $vspace, $black, $font, $product->getSku());
                        $vspace += 30;
                    }
                    if ($config->isProductPrice()) {
                        $vspace += 25 - 15;
                        $currency = $this->_storeManager->getStore()->getCurrentCurrencyCode();
                        $price = strripos((float)$product->getPrice(), ".")
                            ? (float)$product->getPrice() . " " . $currency
                            : (float)$product->getPrice() . ".00 " . $currency;
                        imagettftext($imageLabel, 25, 0, $hspace, $vspace, $black, $font, $price);
                    }
                } else {
                    $this->messageManager->addError("Error! Product data is empty.");
                    return $resultRedirect->setPath('*/*/');
                }
            } else {
                $this->messageManager->addError("Error! Product SKU not found.");
                return $resultRedirect->setPath('*/*/');
            }

            //Barcode
            if ($imageBarcode = imagecreatefromstring(base64_decode($modelGenerator->getEncodedImage()))) {
                $barcodeWidth = imagesx($imageBarcode);
                $barcodeHeight = imagesy($imageBarcode);
                imagecopymerge(
                    $imageLabel,
                    $imageBarcode,
                    (imagesx($imageLabel) - imagesx($imageBarcode) - 20) / 2 + 10,
                    imagesy($imageLabel) - $barcodeHeight - 10,
                    0,
                    0,
                    $barcodeWidth,
                    $barcodeHeight,
                    100
                );
            } else {
                $this->messageManager->addError("Error! Barcode not found. Perhaps it wasn't generated.");
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $this->messageManager->addError("Error! ID not found in a URL params.");
            return $resultRedirect->setPath('*/*/');
        }

        ob_start();
        imagejpeg($imageLabel);
        imagedestroy($imageLabel);
        $imageRaw = ob_get_clean();

        $image = base64_encode($imageRaw);
        return $image;
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
        $this->leftOffset += $image->getPixelWidth() + $rightPadding;
    }

    public function getPageSize($pageFormat)
    {
        $size = explode(':', $pageFormat);
        return ["width" => $size[0], "height" => $size[1]];
    }

    public function getTopCorner($height, $pageFormat)
    {
        return $this->getPageSize($pageFormat)["height"] - $height;
    }
}