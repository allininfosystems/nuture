<?php
namespace Webinse\Barcode\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Upload extends Action
{
    /** @var DirectoryList */
    protected $_directoryList;

    /** @var JsonHelper */
    protected $jsonHelper;

    /**
     * Upload constructor.
     * @param Action\Context $context
     * @param DirectoryList $directoryList
     * @param JsonHelper $jsonHelper
     */
    public function __construct(Action\Context $context, DirectoryList $directoryList, JsonHelper $jsonHelper)
    {
        $this->jsonHelper = $jsonHelper;
        $this->_directoryList = $directoryList;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $tmpDir = $this->_directoryList->getPath('tmp');
            $ext = pathinfo("import_product_to_barcode.csv")["extension"];
            move_uploaded_file($this->getRequest()->getFiles("csv_uploader")["tmp_name"], $tmpDir . "/datasheet." . $ext);
            return $this->jsonResponse(['error' => __("File was successfully uploaded! You can import data.")]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()]);
        }
    }

    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($response));
    }
}