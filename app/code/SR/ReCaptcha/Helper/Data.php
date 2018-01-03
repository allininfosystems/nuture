<?php
namespace SR\ReCaptcha\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MODULE_ENABLED = 'reCaptcha/settings/enabled';
    const SITE_KEY = 'reCaptcha/settings/site_key';
    const SECRET_KEY = 'reCaptcha/settings/secret_key';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Is the module enabled in configuration.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getStoreConfig(self::MODULE_ENABLED);
    }

    /**
     * The recaptcha site key.
     *
     *
     * @return string
     */
    public function getSiteKey()
    {
        return $this->getStoreConfig(self::SITE_KEY);
    }

    /**
     * The recaptcha secret key.
     *
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->getStoreConfig(self::SECRET_KEY);
    }

    public function getStoreConfig($path)
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }
}