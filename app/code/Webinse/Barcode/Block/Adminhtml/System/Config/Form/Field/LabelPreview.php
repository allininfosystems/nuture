<?php
namespace Webinse\Barcode\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Module\Dir\Reader;
use Webinse\Barcode\Controller\Adminhtml\Generator\PrintLabelBarcode;
use Webinse\Barcode\Model\Config;

class LabelPreview extends Field
{
    /** @var Reader */
    protected $_moduleReader;

    /**
     * @var PrintLabelBarcode
     */
    protected $_printLabelBarcode;

    /** @var Config */
    protected $_modelConfig;

    /**
     * LabelPreview constructor.
     * @param Context $context
     * @param Reader $moduleReader
     * @param PrintLabelBarcode $printLabelBarcode
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Reader $moduleReader,
        PrintLabelBarcode $printLabelBarcode,
        Config $config,
        array $data = []
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_printLabelBarcode = $printLabelBarcode;
        $this->_modelConfig = $config;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $config = $this->_modelConfig;
        $viewDir = $this->_moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'Webinse_Barcode'
        );

        //Label params
        $imageLabel = imagecreate($config->getLabelWidth(), $config->getLabelHeight());
        $configBgColor = $config->getLabelBackgroundColor();
        $colorBackground = imagecolorallocate($imageLabel, $configBgColor[0], $configBgColor[1], $configBgColor[2]);
        $black = imagecolorallocate($imageLabel, 0, 0, 0);
        imagecolortransparent($imageLabel, $colorBackground);
        imagerectangle ( $imageLabel, 0, 0, imagesx($imageLabel) - 1, imagesy($imageLabel) - 1, $black);

        //Product image params
        $imagePath = $viewDir . "/adminhtml/web/image/product.jpg";
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
        $font = $viewDir . "/adminhtml/web/css/fonts/roboto.ttf";
        $hspace = $config->getHorizontalSpace();
        $vspace = $config->getVerticalSpace();
        if ($config->isProductName()) {
            $text = "Product Name";
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
            imagettftext($imageLabel, 15, 0, $hspace, $vspace, $black, $font, "SKU-1234");
            $vspace += 30;
        }
        if ($config->isProductPrice()) {
            $vspace += 25 - 15;
            imagettftext($imageLabel, 25, 0, $hspace, $vspace, $black, $font, "100.00 USD");
        }

        //Barcode
        $imageBarcode = imagecreatefromjpeg($viewDir . "/adminhtml/web/image/barcode.jpeg");
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

        ob_start();
        imagejpeg($imageLabel);
        imagedestroy($imageLabel);
        $imageRaw = ob_get_clean();

        return "<img src='data:image/jpeg;base64," . base64_encode($imageRaw) . "'>";
    }
}