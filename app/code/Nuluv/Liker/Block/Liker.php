<?php

namespace Nuluv\Liker\Block;


use Magento\Framework\View\Element\Template;


class Liker extends Template
{


 protected $_registry;

 public function __construct(
    \Magento\Backend\Block\Template\Context $context,       
    \Magento\Framework\Registry $registry,
    array $data = []
    )
 {       
    $this->_registry = $registry;
    parent::__construct($context, $data);
}


public function getCurrentProduct()
{       
    return $this->_registry->registry('current_product')->getId();
}   

public function getPageType()
{

    $this->_request->getFullActionName();
    if($this->_request->getFullActionName() == 'catalog_product_view'){
        $page_type=1;
    }else{
       $page_type=2; 
    }
    return $page_type;
}

public function getLikeCount(){
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $prod_id= $this->getCurrentProduct();
        $like=1;
        $type_id=$this->getPageType();
        $likeModel = $objectManager->create('Nuluv\Liker\Model\Liker');
        $likecollection = $likeModel->getCollection()->addFieldToFilter('likes',array('eq'=>$like))->addFieldToFilter('page_type',array('eq'=>$type_id))->addFieldToFilter('product_id',array('eq'=>$prod_id));

       // echo $likecollection->getSelect();die;

        $totalLikes=$likecollection->count();
        return $totalLikes;
}

function isLikeAlredySubmitted(){

    $prod_id= $this->getCurrentProduct();
    $ip=$this->getRealIpAddr();
    $like=1;
    $type_id=$this->getPageType();
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    $likeModel = $objectManager->create('Nuluv\Liker\Model\Liker');
    $collection= $likeModel->getCollection()->addFieldToFilter('likes',array('eq'=>$like))->addFieldToFilter('ip_address',array('eq'=>$ip))->addFieldToFilter('page_type',array('eq'=>$type_id))->addFieldToFilter('product_id',array('eq'=>$prod_id));
    
    $count=$collection->count();
    return $count;
}


function getRealIpAddr()
    {
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    if($ip == '::1')$ip='127.0.0.1';
    return $ip;
}


}
