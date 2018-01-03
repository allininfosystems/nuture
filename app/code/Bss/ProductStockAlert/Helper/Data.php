<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_ProductStockAlert
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductStockAlert\Helper;

use Magento\Store\Model\Store;
use Magento\Customer\Model\SessionFactory as CustomerSession;

class Data extends \Magento\Framework\Url\Helper\Data
{
    /**
     * Error email template configuration
     */
    const XML_PATH_ERROR_TEMPLATE = 'bss_productstockalert/productstockalert_cron/error_email_template';

    /**
     * Error email identity configuration
     */
    const XML_PATH_ERROR_IDENTITY = 'bss_productstockalert/productstockalert_cron/error_email_identity';

    /**
     * 'Send error emails to' configuration
     */
    const XML_PATH_ERROR_RECIPIENT = 'bss_productstockalert/productstockalert_cron/error_email';

    /**
     * Allow stock alert
     *
     */
    const XML_PATH_STOCK_ALLOW = 'bss_productstockalert/productstockalert/allow_stock';

    /**
     * Customer group allow
     *
     */
    const XML_PATH_CUSTOMER_ALLOW = 'bss_productstockalert/productstockalert/allow_customer';

    /**
     * Limit send count
     *
     */
    const XML_PATH_SEND_LIMIT = 'bss_productstockalert/productstockalert/send_limit';

    /**
     * Qty allow send
     *
     */
    const XML_PATH_QTY_ALLOW = 'bss_productstockalert/productstockalert/allow_stock_qty';

    /**
     * notification message
     *
     */

    const XML_PATH_NOTIFICATION_MESSAGE = 'bss_productstockalert/productstockalert/message';

    /**
     * Current product instance (override registry one)
     *
     * @var null|\Magento\Catalog\Model\Product
     */
    protected $product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /** @var \Magento\Store\Model\StoreManagerInterface */

    protected $storeManager;

    protected $customerSession;

    protected $customer;

    protected $stockColFactory;

    protected $model;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        CustomerSession $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Bss\ProductStockAlert\Model\ResourceModel\Stock\CollectionFactory $stockColFactory,
        \Bss\ProductStockAlert\Model\Stock $model,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->layout = $layout;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession->create();
        $this->customer = $customer;
        $this->stockColFactory = $stockColFactory;
        $this->model = $model;
        parent::__construct($context);
    }

    /**
     * Get current product instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if ($this->product !== null) {
            return $this->product;
        }
        return $this->coreRegistry->registry('product');
    }

    /**
     * Set current product instance
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Bss\ProductStockAlert\Helper\Data
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return Store
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @return Websites
     */
    public function getWebsites()
    {
        return $this->storeManager->getWebsites();
    }

    /**
     * @param string $type
     * @return string
     */
    public function getSaveUrl($type)
    {
        return $this->_getUrl(
            'productstockalert/add/' . $type,
            [
                'product_id' => $this->getProduct()->getId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
            ]
        );
    }

    public function getCronUrl()
    {
        return $this->_getUrl('productstockalert/cron/index');
    }

    /**
     * Retrieve form action
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->_getUrl(
            'productstockalert/ajax/'
        );
    }

    public function getPostAction($product_id)
    {
        return $this->_getUrl(
            'productstockalert/add/stock',
            [
                'product_id' => $product_id,
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
            ]
        );
    }

    /**
     * Create block instance
     *
     * @param string|\Magento\Framework\View\Element\AbstractBlock $block
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createBlock($block)
    {
        if (is_string($block)) {
            if (class_exists($block)) {
                $block = $this->layout->createBlock($block);
            }
        }
        if (!$block instanceof \Magento\Framework\View\Element\AbstractBlock) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid block type: %1', $block));
        }
        return $block;
    }

    /**
     * Check whether stock alert is allowed
     *
     * @return bool
     */
    public function isStockAlertAllowed()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_STOCK_ALLOW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNotificationMessage()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NOTIFICATION_MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

     /**
     * Check customer allowed subscription
     *
     * @return bool
     */

    public function checkCustomer($customerGroupId = null)
    {
        $customerConfig = $this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_ALLOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($customerConfig != '') {
            $customerConfigArr = explode(',', $customerConfig);
            if($customerGroupId !== null) {
                if (in_array($customerGroupId, $customerConfigArr)) {
                    return true;
                }
            }
            else if ($this->customerSession->isLoggedIn()) {
                $customerGroupId = $this->customerSession->getCustomerGroupId();
                if (in_array($customerGroupId, $customerConfigArr)) {
                    return true;
                }
            } else {
                if (in_array(0, $customerConfigArr)) {
                    return true;
                }
            }
        }
        return false;
    }

     /**
     * get limit count send email 
     *
     * @return int
     */

    public function getLimitCount()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SEND_LIMIT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

     /**
     * get qty product to send email 
     *
     * @return int
     */

    public function getQtySendMail()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_QTY_ALLOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getEmailErrorTemplate()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ERROR_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getEmailErrorIdentity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ERROR_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getEmailErrorRecipient()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ERROR_RECIPIENT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomerEmail()
    {
        if ($this->customerSession->isLoggedIn()) {
            $customerData = $this->customer->load($this->customerSession->getCustomerId());
            return $customerData->getEmail();
        }
        return "";
    }

    public function getCustomerId()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomerId();
        }
        return false;
    }

    public function getWebsiteId()
    {
        return $this->storeManager->getStore()->getWebsiteId();
    }

    public function hasEmail($productId)
    {
        if($this->getCustomerId())
        {
            $this->model->setCustomerId($this->customerSession->getCustomerId())
            ->setProductId($productId)
            ->setWebsiteId(
                $this->storeManager->getStore()->getWebsiteId()
            )
            ->loadByParam();

            return $this->model->getAlertStockId();
        }else {
            $notify = $this->customerSession->getNotifySubscription();
            return isset($notify[$productId]);
        }
    }
}
