<?php
namespace Nuluv\Liker\Controller\Liker;
class Index extends \Magento\Framework\App\Action\Action
{
	public function execute()
	{



		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		$likeModel = $objectManager->create('Nuluv\Liker\Model\Liker');

		$ip_address = $this->getRealIpAddr();

		$page_type= $this->getRequest()->getParam('page_type');

		$product_id= $this->getRequest()->getParam('product_id');

        ///here type_id is 0=>unlike and 1=>like
		$type_id= $this->getRequest()->getParam('type_id');

		if($type_id == 0){
			$likedata = $likeModel->getCollection()->addFieldToFilter('likes',array('eq'=>1))->addFieldToFilter('product_id',array('eq'=>$product_id))->addFieldToFilter('page_type',array('eq'=>$page_type))->addFieldToFilter('ip_address',array('eq'=>$ip_address));
			$likeModel->load($likedata->getFirstItem()->getId());
            $likeModel->delete();

		}else{
			$post = array('product_id' => $product_id ,'ip_address' =>$ip_address,'status'=>1,'page_type'=>$page_type,'likes'=>1);

			$likeModel->setData($post);

			$likeModel->save();
		}

		$likecollection = $likeModel->getCollection()->addFieldToFilter('likes',array('eq'=>1))->addFieldToFilter('product_id',array('eq'=>$product_id));

		$totalLikes=$likecollection->count();

		$return_arr = array("likes"=>$totalLikes);

		echo json_encode($return_arr);
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
