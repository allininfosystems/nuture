<?php
/**
 * * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\System\Config\Getauth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magenest\ZohocrmIntegration\Model\Connector;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class GetAuth Controllers
 *
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\System\Config\Getauth
 */
class GetAuth extends Action
{
    const ERROR_CONNECT_TO_ZOHOCRM = 'INVALID_PASSWORD';

    /**
     * @var /Magenest/ZohoCrm/Model/Connector
     */
    protected $_connector;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @param Context $context
     * @param Connector $connector
     */
    public function __construct(
        Context $context,
        Connector $connector,
        JsonFactory $jsonFactory
    ) {
        $this->resultJsonFactory = $jsonFactory;
        parent::__construct($context);
        $this->_connector   = $connector;
    }

    /**
     * Check whether vat is valid
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['username']) || empty($data['password'])) {
                $result['error']       = 1;
                $result['description'] = "Please enter email or password";
                
                $resultJson->setData(json_encode($result));
                return $resultJson;
            }

            $response = $this->_connector->getAuth($data, true);
            if ($response == self::ERROR_CONNECT_TO_ZOHOCRM) {
                $result['error']       = 1;
                $result['description'] = 'Invalid password';
                
                $resultJson->setData(json_encode($result));
                return $resultJson;
            } else {
                $result['error']       = 0;
                $result['description'] = $response;
                
                $resultJson->setData(json_encode($result));
                return $resultJson;
            }
        }
    }
}
