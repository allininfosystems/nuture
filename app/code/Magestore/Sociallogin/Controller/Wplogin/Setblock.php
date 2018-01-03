<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Wplogin;
class Setblock extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {
            $this->_view->loadLayout();
            $this->_view->renderLayout();

            $this->setBlogName();

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function setBlogName()
    {
        $data = $this->getRequest()->getPost();
        $name = $data['name'];
        if ($name) {
            $url = $this->_objectManager->create('Magestore\Sociallogin\Model\Wplogin')->getWpLoginUrl($name);
            $this->getResponse()->setRedirect($url);
        } else {
            $this->messageManager->addError('Please enter Blog name!');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }
    }

}