<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Livelogin extends Sociallogin
{

    public function newLive()
    {

        try {
            require_once $this->_getBaseDir() . 'lib/Author/OAuth2Client.php';

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
        try {
            $live = new \OAuth2Client(
                $this->_dataHelper->getLiveAppkey(),
                $this->_dataHelper->getLiveAppSecret(),
                $this->_dataHelper->getAuthUrlLive()
            );
            $live->api_base_url = "https://apis.live.net/v5.0/";
            $live->authorize_url = "https://login.live.com/oauth20_authorize.srf";
            $live->token_url = "https://login.live.com/oauth20_token.srf";
            $live->out = "https://login.live.com/oauth20_logout.srf";
            return $live;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }

    public function getUrlAuthorCode()
    {
        $live = $this->newLive();
        return $live->authorizeUrl();
    }
}
