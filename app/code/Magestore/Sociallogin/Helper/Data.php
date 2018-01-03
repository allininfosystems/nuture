<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_SECURE_IN_FRONTEND = 'web/secure/use_in_frontend';
    const XML_PATH_CUSTOMER_ACCOUNT_SHARE = 'customer/account_share/scope';

    const XML_PATH_TWLOGIN_CUSTOMER_KEY = 'twlogin/app_id';
    const XML_PATH_TWLOGIN_CUSTOMER_SECRET = 'twlogin/app_secret';

    const XML_PATH_YALOGIN_APP_ID = 'yalogin/app_id';
    const XML_PATH_YALOGIN_CUSTOMER_KEY = 'yalogin/consumer_key';
    const XML_PATH_YALOGIN_CUSTOMER_SECRET = 'yalogin/consumer_secret';

    const XML_PATH_GOLOGIN_CUSTOMER_KEY = 'gologin/consumer_key';
    const XML_PATH_GOLOGIN_CUSTOMER_SECRET = 'gologin/consumer_secret';

    const XML_PATH_VKLOGIN_APP_ID = 'vklogin/app_id';
    const XML_PATH_VKLOGIN_SECURE_KEY = 'vklogin/secure_key';

    const XML_PATH_AMAZONLOGIN_CUSTOMER_KEY = 'amazonlogin/consumer_key';
    const XML_PATH_AMAZONLOGIN_CUSTOMER_SECRET = 'amazonlogin/consumer_secret';
    const XML_PATH_AMAZONLOGIN_REDIRECT_SECRET = 'amazonlogin/redirect_url';

    const XML_PATH_FBLOGIN_APP_ID = 'fblogin/app_id';
    const XML_PATH_FBLOGIN_APP_SECRET = 'fblogin/app_secret';

    const XML_PATH_DIRECT_LOGIN_URL = 'sociallogin/sociallogin/fblogin';

    const XML_PATH_AUTH_URL_FQ = 'sociallogin/sociallogin/fqlogin';
    const XML_PATH_AUTH_URL_LIVE = 'sociallogin/sociallogin/livelogin';

    const XML_PATH_LOGIN_URL = 'customer/account/login';

    const XML_PATH_EDIT_URL = 'customer/account/edit';

    const XML_PATH_FQLOGIN_CUSTOMER_KEY = 'fqlogin/consumer_key';
    const XML_PATH_FQLOGIN_CUSTOMER_SECRET = 'fqlogin/consumer_secret';

    const XML_PATH_LIVELOGIN_CUSTOMER_KEY = 'livelogin/consumer_key';
    const XML_PATH_LIVELOGIN_CUSTOMER_SECRET = 'livelogin/consumer_secret';

    const XML_PATH_LINKLOGIN_APP_ID = 'linklogin/app_id';
    const XML_PATH_LINKLOGIN_APP_SECRET = 'linklogin/secret_key';

    const XML_PATH_POSITION = 'general/position';

    const XML_PATH_INSTALOGIN_CUSTOMER_KEY = 'instalogin/consumer_key';
    const XML_PATH_INSTALOGIN_CUSTOMER_SECRET = 'instalogin/consumer_secret';
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * \Magento\Catalog\Model\CategoryFactory
     * @var [type]
     */
    protected $_categoryFactory;

    protected $_scopeConfig;

    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;
    /**
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
    /**
     *
     * @var \Magento\Customer\Model\Resource\Customer\CollectionFactory
     */
    protected $_customerCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
        $this->_customerFactory = $customerFactory;
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_url = $context->getUrlBuilder();
        $this->_logger  =  $context->getLogger();

    }

    // get customer by email
    public function getCustomerByEmail($email, $website_id)
    {

        $customer = $this->_customerCollectionFactory->create();

        $collection = $customer->addAttributeToSelect('*')
            ->addAttributeToFilter('email', $email);

        if ($this->getConfig(self::XML_PATH_CUSTOMER_ACCOUNT_SHARE)) {
            $collection->addFieldToFilter('website_id', $website_id);
        }

        return $collection->getFirstItem();
    }

    //create customer login multisite
    public function createCustomerMultiWebsite($data, $website_id, $store_id)
    {
        $customer = $this->_customerFactory->create();
        $customer->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setWebsiteId($website_id)
            ->setStoreId($store_id)
            ->save();

        try {
            $customer->save();
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
        return $customer;
    }

    public function getInstaCustomerKey()
    {
        return trim($this->getConfig(self::XML_PATH_INSTALOGIN_CUSTOMER_KEY));
    }

    public function getInstaCustomerSecret()
    {
        return trim($this->getConfig(self::XML_PATH_INSTALOGIN_CUSTOMER_SECRET));
    }

    public function getTwConsumerKey()
    {
        return trim($this->getConfig(self::XML_PATH_TWLOGIN_CUSTOMER_KEY));
    }

    public function getTwConsumerSecret()
    {
        return trim($this->getConfig(self::XML_PATH_TWLOGIN_CUSTOMER_SECRET));
    }

    public function getYaAppId()
    {
        return trim($this->getConfig(self::XML_PATH_YALOGIN_APP_ID));
    }

    public function getYaConsumerKey()
    {
        return trim($this->getConfig(self::XML_PATH_YALOGIN_CUSTOMER_KEY));
    }

    public function getYaConsumerSecret()
    {
        return trim($this->getConfig(self::XML_PATH_YALOGIN_CUSTOMER_SECRET));
    }

    public function getGoConsumerKey()
    {
        return trim($this->getConfig(self::XML_PATH_GOLOGIN_CUSTOMER_KEY));
    }

    public function getGoConsumerSecret()
    {
        return trim($this->getConfig(self::XML_PATH_GOLOGIN_CUSTOMER_SECRET));
    }

    public function getVkAppId()
    {
        return trim($this->getConfig(self::XML_PATH_VKLOGIN_APP_ID));
    }

    public function getVkSecureKey()
    {
        return trim($this->getConfig(self::XML_PATH_VKLOGIN_SECURE_KEY));
    }

    public function getAmazonConsumerKey()
    {
        return trim($this->getConfig(self::XML_PATH_AMAZONLOGIN_CUSTOMER_KEY));
    }

    public function getAmazonSecret()
    {
        return trim($this->getConfig(self::XML_PATH_AMAZONLOGIN_CUSTOMER_SECRET));
    }

    public function getAmazonUrlcallback()
    {
        return trim($this->getConfig(self::XML_PATH_AMAZONLOGIN_REDIRECT_SECRET));
    }

    public function getFbAppId()
    {
        return trim($this->getConfig(self::XML_PATH_FBLOGIN_APP_ID));
    }

    public function getFbAppSecret()
    {
        return trim($this->getConfig(self::XML_PATH_FBLOGIN_APP_SECRET));
    }

    public function getAuthUrl()
    {
        $isSecure = $this->getConfig(self::XML_PATH_SECURE_IN_FRONTEND);
        return $this->_url->getUrl(self::XML_PATH_DIRECT_LOGIN_URL, array('_secure' => $isSecure, 'auth' => 1));
    }

    public function getDirectLoginUrl()
    {
        $isSecure = $this->getConfig(self::XML_PATH_SECURE_IN_FRONTEND);
        return $this->_url->getUrl(self::XML_PATH_DIRECT_LOGIN_URL, array('_secure' => $isSecure));
    }

    public function getLoginUrl()
    {
        $isSecure = $this->getConfig(self::XML_PATH_SECURE_IN_FRONTEND);
        return $this->_url->getUrl(self::XML_PATH_LOGIN_URL, array('_secure' => $isSecure));
    }

    public function getEditUrl()
    {
        $isSecure = $this->getConfig(self::XML_PATH_SECURE_IN_FRONTEND);
        return $this->_url->getUrl(self::XML_PATH_EDIT_URL, array('_secure' => $isSecure));
    }

    public function getFqAppkey()
    {
        return trim($this->getConfig(self::XML_PATH_FQLOGIN_CUSTOMER_KEY));
    }

    public function getFqAppSecret()
    {
        return trim($this->getConfig(self::XML_PATH_FQLOGIN_CUSTOMER_SECRET));
    }

    public function getLiveAppkey()
    {
        return trim($this->getConfig(self::XML_PATH_LIVELOGIN_CUSTOMER_KEY));
    }

    public function getLiveAppSecret()
    {
        return trim($this->getConfig(self::XML_PATH_LIVELOGIN_CUSTOMER_SECRET));
    }

    public function getAuthUrlFq()
    {
        $isSecure = $this->getConfig(self::XML_PATH_SECURE_IN_FRONTEND);
        return $this->_url->getUrl(self::XML_PATH_AUTH_URL_FQ, array('_secure' => $isSecure, 'auth' => 1));
    }

    public function getAuthUrlLive()
    {
        $isSecure = $this->getConfig(self::XML_PATH_SECURE_IN_FRONTEND);
        return $this->_getUrl(self::XML_PATH_AUTH_URL_LIVE, array('_secure' => $isSecure, 'auth' => 1));
    }

    public function getLinkedConsumerKey()
    {
        return trim($this->getConfig(self::XML_PATH_LINKLOGIN_APP_ID));
    }

    public function getLinkedConsumerSecret()
    {
        return trim($this->getConfig(self::XML_PATH_LINKLOGIN_APP_SECRET));
    }

    public function getResponseBody($url)
    {
        if (ini_get('allow_url_fopen') != 1) {
            @ini_set('allow_url_fopen', '1');
        }
        if (ini_get('allow_url_fopen') == 1) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $contents = curl_exec($ch);
            curl_close($ch);
            // var_dump($contents);die();
        } else {
            $contents = file_get_contents($url);
        }

        return $contents;
    }

    public function getShownPositions()
    {
        $shownpositions = $this->getConfig(self::XML_PATH_POSITION, $this->getStore()->getStoreId());
        $shownpositions = explode(',', $shownpositions);
        return $shownpositions;
    }

    public function getPerResultStatus($result)
    {
        $result = str_replace(array('{', '}', '"', ':'), array('', '', '', ','), $result);
        $rs = explode(",", $result);
        if ($rs[10]) {
            return $rs[10];
        } else {
            return "";
        }
    }

    public function getPerEmail($result)
    {
        $result = str_replace(array('"', ':'), array('', ','), $result);
        $rs = explode(",", $result);
        if ($rs[8]) {
            return $rs[8];
        } else {
            return "";
        }
    }

    /**
     * @param $key
     * @param null $store
     * @return mixed
     */
    public function getConfig($key, $store = null)
    {
        return $this->_scopeConfig->getValue(
            'sociallogin/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

}
