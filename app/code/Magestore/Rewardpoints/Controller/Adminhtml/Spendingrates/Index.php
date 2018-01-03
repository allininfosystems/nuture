<?php
namespace Magestore\Rewardpoints\Controller\Adminhtml\Spendingrates;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Rewardpoints::Spending_Rates');
        $resultPage->addBreadcrumb(__('Spending Rates'), __('Spending Rates'));
        $resultPage->addBreadcrumb(__('Manage Spending Rates'), __('Manage Spending Rates'));
        $resultPage->getConfig()->getTitle()->prepend(__('Spending Rates'));

        return $resultPage;
    }

    /**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Spending_Rates');
    }


}
