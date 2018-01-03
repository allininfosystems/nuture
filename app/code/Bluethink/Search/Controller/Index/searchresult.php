<?php

namespace Bluethink\Search\Controller\Index;

class searchresult extends \Magento\Framework\App\Action\Action {

    protected $_cacheTypeList;
    protected $_cacheState;
    protected $_cacheFrontendPool;
    protected $resultPageFactory;
    protected $_registry;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\App\Cache\StateInterface $cacheState, \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
    }

    public function execute() {

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Search Result'));
        return $resultPage;
    }

}
