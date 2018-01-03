<?php

namespace Nuluv\Lookingfor\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
class Index extends \Magento\Framework\App\Action\Action
{
	 
	protected $_messageManager;
	 
	public function __construct(Context $context, ManagerInterface $messageManager) {
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }
	
    public function execute()
    {
		//echo '<pre>';print_r($_POST);die('testing');
		$Url = "https://www.google.com/recaptcha/api/siteverify";
		$secret = "6Lcydz4UAAAAAOHQ1qbLopxTmR8zT_gm5r-vpU1E";
		if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
			//get verified response data
			$data = array('secret' => $secret, 'response' => $_POST['g-recaptcha-response']);

			$ch = curl_init($Url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			$verifyResponse = curl_exec($ch);
			curl_close($ch);

			$responseData = json_decode($verifyResponse,true);
			//echo $responseData['success'];//echo '<pre>';print_r($responseData['success']);
			//die('testing');

			if($responseData['success']==1){
				if(isset($_POST['email'])){
					$name = $_POST['name'];
					$email = $_POST['email'];
					$mobile = $_POST['mobile'];
					$message = $_POST['message'];
					$this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
					$connection= $this->_resources->getConnection();
					$msg = '';
					$sql = "INSERT INTO lookingfor(name, email, mobile, message) VALUES ('$name', '$email', '$mobile', '$message')";
					if($connection->query($sql)){
					   $this->_messageManager->addSuccess(__("Your message sent successfully"));
					} else {
					   $this->_messageManager->addError(__("Try again"));
					}
				}
			}
			else{
				$this->_messageManager->addError(__("Please click on the reCAPTCHA box."));
			}
			// your code

		}else{
			if(isset($_POST['email'])){
				$this->_messageManager->addError(__("Please click on the reCAPTCHA box."));
			}
		}

        
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
		
    }
}