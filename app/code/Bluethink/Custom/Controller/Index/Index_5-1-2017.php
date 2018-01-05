<?php
/**
 *
 * Copyright © 2015 Bluethinkcommerce. All rights reserved.
 */
namespace Bluethink\Custom\Controller\Index;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Flush cache storage
     *
     */
    public function execute()
    {

      $post=$this->getRequest()->getPost('postcode');

		echo 'rfgfdgfdg'.$post; 
		
	$response = '{"response":'.$post.'}';
	
   // echo  $response;
   
 $zip='201301';
   
 $url =  'http://postalpincode.in/api/pincode/'.$zip

 $obj = json_decode($yourJSONString); to convert it to an object.

foreach($obj->response->docs as $doc)
{
$doc->student_id 
$doc->student_name[0].
}
        //print_r($this->getRequest()->getPost('input'));
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$customerSession = $objectManager->create('Magento\Customer\Model\Session');
        //$customerSession->setPincode($this->getRequest()->getPost('input'));
  
        
    }
}
