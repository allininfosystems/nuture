<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace  Magestore\Rewardpoints\Controller\Adminhtml\Earningrates;

use Magento\Backend\App\Action;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEdit extends \Magento\Backend\App\Action
{
    /** @var \Magento\Framework\Controller\Result\JsonFactory  */
    protected $resultJsonFactory;

    /** @var \Magento\Framework\Api\DataObjectHelper  */
    protected $dataObjectHelper;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * @param Action\Context $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
       
        $model = $this->_objectManager->create('Magestore\Rewardpoints\Model\Rate');
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $rateId) {
            $postItems[$rateId]['money'] = str_replace('$','',$postItems[$rateId]['money']);
            $model->setData($this->getData($postItems[$rateId], true));
            $model->setId($rateId);
            $model->save();
        }

        return $resultJson->setData([
            'messages' => $this->getErrorMessages(),
            'error' => $this->isErrorExists(),
            
        ]);
    }

    /**
     * @param array $data
     * @param null $isCustomerData
     * @return mixed
     */
    protected function getData(array $data, $isCustomerData = null)
    {
        $addressKeys = preg_grep(
            '/^(' . AttributeRepository::BILLING_ADDRESS_PREFIX . '\w+)/',
            array_keys($data),
            $isCustomerData
        );
        $result = array_intersect_key($data, array_flip($addressKeys));
        if ($isCustomerData === null) {
            foreach ($result as $key => $value) {
                if (strpos($key, AttributeRepository::BILLING_ADDRESS_PREFIX) !== false) {
                    unset($result[$key]);
                    $result[str_replace(AttributeRepository::BILLING_ADDRESS_PREFIX, '', $key)] = $value;
                }
            }
        }
        return $result;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Earning_Rates');
    }


    /**
     * Get array with errors
     *
     * @return array
     */
    protected function getErrorMessages()
    {
        $messages = [];
        foreach ($this->getMessageManager()->getMessages()->getItems() as $error) {
            $messages[] = $error->getText();
        }
        return $messages;
    }

    /**
     * Check if errors exists
     *
     * @return bool
     */
    protected function isErrorExists()
    {
        return (bool)$this->getMessageManager()->getMessages(true)->getCount();
    }

}
