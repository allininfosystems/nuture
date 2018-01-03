<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;
class Toplinks extends Sociallogin
{

    protected function _beforeToHtml()
    {
        if (!$this->getIsActive()) {
            $this->setTemplate(null);
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->setTemplate(null);
        }

        return parent::_beforeToHtml();
    }

    public function getShownPositions()
    {
        $shownpositions = $this->_dataHelper->getConfig(\Magestore\Sociallogin\Helper\Data::XML_PATH_POSITION, $this->_storeManager->getStore()->getId());
        $shownpositions = explode(',', $shownpositions);
        return $shownpositions;
    }

    public function getPopupLoginUrl()
    {

        return $this->_storeManager->getStore()->getUrl('sociallogin/popup/login', ['_secure' => true]);
    }

    public function getPopupSendPass()
    {

        return $this->_storeManager->getStore()->getUrl('sociallogin/popup/sendPass', ['_secure' => true]);
    }

    public function getPopupCreateAcc()
    {
        return $this->_storeManager->getStore()->getUrl('sociallogin/popup/createAcc', ['_secure' => true]);

    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getUrl('customer/account');
    }
}