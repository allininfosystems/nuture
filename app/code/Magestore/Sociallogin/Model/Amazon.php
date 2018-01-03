<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

class Amazon extends Sociallogin
{

    public function getAccessTokenFromAuthCode($authcode)
    {
        $c = curl_init('https://api.amazon.com/auth/o2/token');
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code={$authcode}
            &client_id={$this->_dataHelper->getAmazonConsumerKey()}&client_secret={$this->_dataHelper->getAmazonSecret()}");
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        // make the request
        $r = curl_exec($c);

        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        // decode the response
        $d = json_decode($r);
        if ($status != 200 or $d->error) {
            var_dump($d);
            return false;
        }
        return $d->access_token;
    }

    /**
     * Requests Amazon User-Profile from access_token
     *
     * @param string $access_token
     * @return object $profile Amazon-User-Profile
     */
    public function getUserProfileFromAccessToken($access_token)
    {
        // setup a request to the profile endpoint
        $c = curl_init('https://api.amazon.com/user/profile');
        curl_setopt($c, CURLOPT_HTTPHEADER, array('Authorization: bearer ' . $access_token));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        // make the request
        $r = curl_exec($c);
        curl_close($c);

        // decode the response
        $d = json_decode($r);
        return $d;
    }
}
