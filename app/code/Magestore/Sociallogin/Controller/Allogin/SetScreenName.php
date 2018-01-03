<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Allogin;
class SetScreenName extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {

            $this->setScreenName();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function setScreenName()
    {
        $data = $this->getRequest()->getPost();
        $name = $data['name'];

        if ($name) {
            $url = $this->_objectManager->create('Magestore\Sociallogin\Model\Allogin')->getAlLoginUrl($name);
            $this->getResponse()->setRedirect($url);
        } else {
            $this->_getSingtone()->addError('Please enter Blog name!');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }
    }

}