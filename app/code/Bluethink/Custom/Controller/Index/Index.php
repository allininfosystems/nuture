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
    $zip=$this->getRequest()->getParam('postcode');

    function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }
		
        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(__FUNCTION__, $d);
        }
        else {
            // Return array
            return $d; 
        }
    }
	
$response = file_get_contents('http://postalpincode.in/api/pincode/'.$zip);
$obj = json_decode($response); 
//print_r($obj);

$array = objectToArray($obj);

if($array['Status']=='Error')

{
echo $response = '{"city":"","state":""}';
}

else
{
foreach($array['PostOffice'] as $arr)
{

$city[] = $arr['District'];
$state[] = $arr['State']; 
}

echo $response = '{"city":"'.$city[0].'","state":"'.$state[0].'"}';

}
//echo  $response;


/*  $url =  'http://postalpincode.in/api/pincode/'.$zip

 $obj = json_decode($yourJSONString); to convert it to an object.

foreach($obj->response->docs as $doc)
{
$doc->student_id 
$doc->student_name[0].
}
 */        //print_r($this->getRequest()->getPost('input'));
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$customerSession = $objectManager->create('Magento\Customer\Model\Session');
        //$customerSession->setPincode($this->getRequest()->getPost('input'));
  
        
    }
}
