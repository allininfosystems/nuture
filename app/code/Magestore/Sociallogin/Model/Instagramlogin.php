<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Instagramlogin extends Sociallogin
{

    const XML_PATH_INSTALOGIN = 'sociallogin/sociallogin/instagramlogin/';

    public function newInstagram()
    {
        try {
            require_once $this->_getBaseDir() . 'lib/instagram/instagram.class.php';

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

        $instagram = new \Instagram([
            'apiKey' => trim($this->_dataHelper->getInstaCustomerKey()),
            'apiSecret' => trim($this->_dataHelper->getInstaCustomerSecret()),
            'apiCallback' => $this->_storeManager->getStore()->getUrl(self::XML_PATH_INSTALOGIN, ['_secure' => true]),
        ]);
        return $instagram;
    }

    public function getInstagramLoginUrl()
    {
        return $this->newInstagram()->getLoginUrl();
    }
}
