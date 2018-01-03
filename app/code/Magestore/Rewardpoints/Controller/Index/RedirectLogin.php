<?php
namespace Magestore\Rewardpoints\Controller\Index;

class RedirectLogin extends \Magestore\Rewardpoints\Controller\AbstractAction
{
    public function execute()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            $url =$this->getRequest()->getParam('redirect');
            if($url){
                $url =  urldecode($this->getRequest()->getParam('redirect'));
            }else{
                $url = $this->_sessionManager->getData('redirect');
            }
            if (strpos($url, 'checkout/onepage')) {
                $url = $this->getUrl('checkout/onepage/index');
            }
            $this->_customerSession->setAfterAuthUrl($url);
        }
        $this->_redirect('customer/account/login');
    }

}
