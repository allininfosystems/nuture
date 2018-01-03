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
 * Mass export action for the Barcode Generator.
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */

namespace Webinse\Barcode\Controller\Adminhtml\Generator;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webinse\Barcode\Model\ResourceModel\Generator\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Filesystem\DirectoryList;

class MassExport extends \Magento\Backend\App\Action
{
    /** @var Filter */
    protected $filter;

    /** @var CollectionFactory */
    protected $collectionFactory;

    /** @var Csv */
    protected $_csvProcessor;

    /** @var DirectoryList */
    protected $_directoryList;

    /**
     * MassExport constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Csv $csvProcessor
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Csv $csvProcessor,
        DirectoryList $directoryList
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_csvProcessor = $csvProcessor;
        $this->_directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        try {
            $tmpDir = $this->_directoryList->getPath('tmp');
            $filename = $tmpDir . "/data.csv";
            $data[] = ["PRODUCT_NAME","SKU","BARCODE","IMAGE_FORMAT","BARCODE_TYPE","ENCODED_IMAGE"];
            $collectionDataRaw = $collection->getData();
            $collectionData = [];
            foreach ($collectionDataRaw as $item) {
                if ($item["entity_id"]) {
                    unset($item["entity_id"]);
                }
                $collectionData[] = $item;
            }
            $data = array_merge($data, $collectionData);
            $this->_csvProcessor->saveData($filename, $data);
            $this->_downloadCsv($filename);
            unlink($filename);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    protected function _downloadCsv($filename)
    {
        if (file_exists($filename)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=exported_barcodes_' . basename($filename));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            readfile($filename);
        }
    }
}