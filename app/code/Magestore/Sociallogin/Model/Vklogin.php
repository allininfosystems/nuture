<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Vklogin extends Sociallogin
{

    public function getVk()
    {
        $appId = $this->_dataHelper->getVkAppId();
        $secretId = $this->_dataHelper->getVkSecureKey();
        try {
            require_once $this->_getBaseDir() . 'lib/Vk/VK.php';
            require_once $this->_getBaseDir() . 'lib/Vk/VKException.php';

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
        $vk = new \VK($appId, $secretId);
        return $vk;
    }

    public function _construct()
    {

        $this->_init('Magestore\Sociallogin\Model\ResourceModel\Vklogin');
    }
}
