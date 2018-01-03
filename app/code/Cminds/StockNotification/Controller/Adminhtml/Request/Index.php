<?php

namespace Cminds\StockNotification\Controller\Adminhtml\Request;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    private $resultPageFactory;
    private $resultPage;

    /**
     * Index constructor.
     *
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $this->setPageData();

        return $this->getResultPage();
    }

    /**
     * Check permission via ACL resource
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cminds_StockNotification::customer_requests');
    }

    /**
     * Get ResultPage
     *
     * @return \Magento\Framework\View\Result\Page
     */
    private function getResultPage()
    {
        if ($this->resultPage === null) {
            $this->resultPage = $this->resultPageFactory->create();
        }

        return $this->resultPage;
    }

    /**
     * Set Page Data
     *
     * @return $this
     */
    private function setPageData()
    {
        $resultPage = $this->getResultPage();
        $resultPage->setActiveMenu('Magento_Catalog::catalog');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Requests'));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Stock Notification'), __('Stock Notification'));
        $resultPage->addBreadcrumb(__('Stock Notification'), __('Manage Customer Requests'));

        return $this;
    }
}
