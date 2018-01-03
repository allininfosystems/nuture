<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Sociallogin\Block\Adminhtml\System\Config;

class FoursQuareredirecturl
    extends \Magestore\Sociallogin\Block\Adminhtml\System\Container
{
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);

        $redirectUrl = $this->_storeManager->getStore($storeId)->getUrl('', array('_secure' => true));
        $domain = parse_url($redirectUrl);
        $referer = isset($domain['host']) ? $domain['scheme'] . '://' . $domain['host'] : $redirectUrl;

        $html = "<input style='width: 100%;' readonly id='sociallogin_fqlogin_redirecturl' class='input-text' value='" . $referer . "'>";
        return $html;
    }

}