<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Callogin;
class SetClaivdName extends \Magestore\Sociallogin\Controller\Sociallogin
{

    public function execute()
    {

        try {
            $this->_setClaivdName();

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }

    public function _setClaivdName()
    {
        $data = $this->getRequest()->getPost();

        if ($data) {
            $name = $data['name'];
            $url = $this->getCalModel()->getCalLoginUrl($name);
            $this->getResponse()->setRedirect($url);
        } else {
            $this->_getSingtone()->addError('Please enter Blog name!');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }

    }

    public function getCalModel()
    {
        return $this->_objectManager->create('Magestore\Sociallogin\Model\Callogin');
    }

}