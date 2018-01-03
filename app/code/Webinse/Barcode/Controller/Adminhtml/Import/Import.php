<?php
namespace Webinse\Barcode\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Filesystem\DirectoryList;
use Webinse\Barcode\Model\Generator;

class Import extends Action
{
    /** @var Csv */
    protected $_csvProcessor;

    /** @var DirectoryList */
    protected $_directoryList;

    /** @var Generator */
    protected $_modelGenerator;

    /**
     * Import constructor.
     * @param Action\Context $context
     * @param Csv $csvProcessor
     * @param DirectoryList $directoryList
     * @param Generator $modelGenerator
     */
    public function __construct(
        Action\Context $context,
        Csv $csvProcessor,
        DirectoryList $directoryList,
        Generator $modelGenerator
    ) {
        parent::__construct($context);
        $this->_csvProcessor = $csvProcessor;
        $this->_directoryList = $directoryList;
        $this->_modelGenerator = $modelGenerator;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $tmpDir = $this->_directoryList->getPath('tmp');
            $filename = $tmpDir . "/datasheet.csv";
            $importProductRawData = $this->_csvProcessor->getData($filename);
            $model = $this->_modelGenerator;
            foreach ($importProductRawData as $row => $item) {
                if ($row == 0) continue;
                $data["product_name"] = $item[0];
                $data["sku"]          = $item[1];
                $data["barcode"]      = $item[2];
                $data["barcode_type"] = $item[3];
                $data["image_format"] = $item[4];
                $data["encoded_image"] = $item[5];
                $model->setData($data);
                $model->save();
            }
            unlink($filename);
            $this->messageManager->addSuccess(__("The data have been successfully imported."));
            return $resultRedirect->setPath('*/generator/');
        } catch (\Exception $e) {
            $this->messageManager->addError(__("The data was not imported. Reason: CSV file wasn't uploaded."));
            return $resultRedirect->setPath('*/*/');
        }
    }
}