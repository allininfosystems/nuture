<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Yalogin extends Sociallogin
{

    public function hasSession()
    {
        try {

            require_once $this->_getBaseDir() . 'lib/Yahoo/Yahoo.inc';

            error_reporting(E_ALL | E_NOTICE);
            ini_set('display_errors', true);
            \YahooLogger::setDebug(true);
            \YahooLogger::setDebugDestination('LOG');

//            session_save_path('/tmp/');

            if (array_key_exists("logout", $this->_request->getParams())) {
                \YahooSession::clearSession($sessionStore = NULL);
            }

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
        $consumerKey = $this->_dataHelper->getYaConsumerKey();
        $consumerSecret = $this->_dataHelper->getYaConsumerSecret();
        $appId = $this->getAppId();
        return \YahooSession::hasSession($consumerKey, $consumerSecret, $appId, $sessionStore = NULL, $verifier = NULL);
    }

    public function getAuthUrl()
    {

        $consumerKey = $this->_dataHelper->getYaConsumerKey();
        $consumerSecret = $this->_dataHelper->getYaConsumerSecret();
        $callback = \YahooUtil::current_url() . '?in_popup';
        return \YahooSession::createAuthorizationUrl($consumerKey, $consumerSecret, $callback, $sessionStore = NULL);
    }

    public function getSession()
    {

        $consumerKey = $this->_dataHelper->getYaConsumerKey();
        $consumerSecret = $this->_dataHelper->getYaConsumerSecret();
        $appId = $this->_dataHelper->getYaAppId();
        return \YahooSession::requireSession($consumerKey, $consumerSecret, $appId, $callback = NULL, $sessionStore = NULL, $verifier = NULL);
    }
}