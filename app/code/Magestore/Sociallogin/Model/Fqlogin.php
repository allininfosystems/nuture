<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Fqlogin extends Sociallogin
{

    public function newFoursquare()
    {
        try {
            require_once $this->_getBaseDir() . 'lib/Foursquare/FoursquareAPI.class.php';

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

        $foursquare = new \FoursquareApi(
            $this->_dataHelper->getFqAppkey(),
            $this->_dataHelper->getFqAppSecret(),
            urlencode($this->_dataHelper->getAuthUrlFq())
        );
        return $foursquare;
    }

    public function getFqLoginUrl()
    {
        $foursquare = $this->newFoursquare();
        $loginUrl = $foursquare->AuthenticationLink();
        return $loginUrl;
    }
}
