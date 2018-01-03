<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block\Adminhtml\System\Config;

class Liveredirecturl
    extends \Magestore\Sociallogin\Block\Adminhtml\System\Container
{
    const XML_PATH_SECURE_IN_FRONTEND = 'web/secure/use_in_frontend';
    const XML_PATH_LIVE_LOGIN = 'sociallogin/sociallogin/livelogin';

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);

        $isSecure = $this->_dataHelper->getConfig(self::XML_PATH_SECURE_IN_FRONTEND);
        $redirectUrl = $this->_storeManager->getStore($storeId)->getUrl(self::XML_PATH_LIVE_LOGIN, array('_secure' => $isSecure, 'auth' => 1));
        $array = parse_url($redirectUrl);
        if (isset($array['query']) && $array['query']) {
            $redirectUrl = str_replace('?' . $array['query'], '', $redirectUrl);
        }

        $html = "<input style='width: 100%;'  readonly id='sociallogin_livelogin_redirecturl' class='input-text' value='" . $redirectUrl . "'>";
        return $html;
    }

}