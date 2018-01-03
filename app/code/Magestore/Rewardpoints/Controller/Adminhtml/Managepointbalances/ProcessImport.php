<?php
namespace Magestore\Rewardpoints\Controller\Adminhtml\Managepointbalances;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class ProcessImport
 */
class ProcessImport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->fileFactory = $fileFactory;
        $this->csvProcessor = $csvProcessor;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * import action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $uploader = $this->_objectManager->create(
            'Magento\MediaStorage\Model\File\Uploader',
            ['fileId' => 'filecsv']
        );
        $files = $uploader->validateFile();

        if (isset($files['name']) && $files['name'] != '') {
            try {
                if(pathinfo($files['name'],PATHINFO_EXTENSION) !='csv'){
                    $this->messageManager->addError(__('Does not support the %1 file', pathinfo($files['name'],PATHINFO_EXTENSION)));
                    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath('rewardpoints/managepointbalances/import');
                    return $resultRedirect;
                }
                $file = $this->getRequest()->getFiles('filecsv');
                $dataFile = $this->csvProcessor->getData($file['tmp_name']);
                $customerData = array();
                foreach ($dataFile as $row => $cols) {
                    if ($row == 0) {
                        $fields = $cols;
                    } else {
                        $customerData[] = array_combine($fields, $cols);
                    }
                }

                if (isset($customerData) && count($customerData)) {
                    $cnt = $this->_updateCustomer($customerData);
                    $cntNot = count($customerData) - $cnt;
                    $successMessage = __('Imported total %1 customer point balance(s)', $cnt);
                    if ($cntNot) {
                        $successMessage .= "</br>";
                        $successMessage .= __("There are %1 emails which don't belong to any accounts.", $cntNot);
                    }
                    $this->messageManager->addSuccess($successMessage);

                    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath('rewardpoints/managepointbalances/index');
                    return $resultRedirect;
                } else {
                    $this->messageManager->addError(__('Point balance imported'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            $this->messageManager->addError(__('No uploaded files'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('rewardpoints/managepointbalances/import');
        return $resultRedirect;
    }

    protected function _updateCustomer($customerData)
    {
        $collection = array();
        $website = $this->_objectManager->create('Magento\Config\Model\Config\Source\Website')->toOptionArray();
        $website[] = array(
            'value' => 0,
            'label' => 'Admin'
        );

        foreach ($customerData as $key => $value) {
            $website_id = $this->storeManager->getDefaultStoreView()->getWebsiteId();
            foreach ($website as $key => $id) {
                if ($id['label'] == $value['Website']) {
                    $website_id = $id['value'];
                    break;
                }
            }
            $email = $value['Email'];
            $pointBalance = $value['Point Change'];
            $expireAfter = $value['Points expire after'];
            $customerExist = $this->_checkCustomer($email, $website_id);
            if (!$customerExist || !$customerExist->getId()) {
                continue;
            }
            $customerExist->setPointBalance($pointBalance)
                ->setExpireAfter($expireAfter);
            $collection[] = $customerExist;
        }
        $this->_objectManager->create('Magestore\Rewardpoints\Model\ResourceModel\Transaction')->importPointFromCsv($collection);
        return count($collection);
    }

    /**
     * check customer exist by email
     * @param type $email
     * @param type $website_id
     * @return type
     */
    protected function _checkCustomer($email, $website_id = 1)
    {
        return $this->_objectManager->create('Magento\Customer\Model\Customer')->setWebsiteId($website_id)->loadByEmail($email);
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Manage_Point_Balances');
    }

}
