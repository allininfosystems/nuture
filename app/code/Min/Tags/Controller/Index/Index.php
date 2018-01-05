<?php
/**
 * Index Controller
 * @author Min <dangquocmin@gmail.com>
 */
namespace Min\Tags\Controller\Index;

use Magento\Framework\App\Action\Context;

class Index extends  \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;

    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Renders Product by tag
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}