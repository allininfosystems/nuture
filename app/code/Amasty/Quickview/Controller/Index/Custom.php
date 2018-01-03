<?php

namespace Amasty\Quickview\Controller\Index;

use Magento\Framework\Controller\ResultFactory;    

class Custom extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\View\Result\PageFactory $resultFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');
        
        $response->setContents(
            $this->jsonHelper->jsonEncode(
                [
                    'data' => $data,
                ]
            )
        );
        return $response;
    } 
}