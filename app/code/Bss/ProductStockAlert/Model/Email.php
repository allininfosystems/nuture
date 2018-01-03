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
namespace Bss\ProductStockAlert\Model;

/**
 * ProductAlert Email processor
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Email extends \Magento\Framework\Model\AbstractModel
{
    const XML_PATH_EMAIL_STOCK_TEMPLATE = 'bss_productstockalert/productstockalert/email_stock_template';

    const XML_PATH_EMAIL_IDENTITY = 'bss_productstockalert/productstockalert/email_identity';

    /**
     * Type
     *
     * @var string
     */
    protected $_type = 'stock';

    /**
     * Website Model
     *
     * @var \Magento\Store\Model\Website
     */
    protected $_website;

    /**
     * Customer Name
     *
     * @var string
     */
    protected $_customerName;

    /**
     * Customer Email
     *
     * @var string
     */
    protected $_customerEmail;

    /**
     * Product collection which of back in stock
     *
     * @var array
     */
    protected $_stockProducts = [];

    /**
     * Stock block
     *
     * @var \Bss\ProductStockAlert\Block\Email\Stock
     */
    protected $_stockBlock;

    /**
     * Product alert data
     *
     * @var \Bss\ProductStockAlert\Helper\Data
     */
    protected $_productAlertData = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $_appEmulation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Bss\ProductStockAlert\Helper\Data $productAlertData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Helper\View $customerHelper
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Bss\ProductStockAlert\Helper\Data $productAlertData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_productAlertData = $productAlertData;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->_appEmulation = $appEmulation;
        $this->_transportBuilder = $transportBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Set model type
     *
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * Retrieve model type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set website model
     *
     * @param \Magento\Store\Model\Website $website
     * @return $this
     */
    public function setWebsite(\Magento\Store\Model\Website $website)
    {
        $this->_website = $website;
        return $this;
    }

    /**
     * Set website id
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->_website = $this->_storeManager->getWebsite($websiteId);
        return $this;
    }

    /**
     * Set customer name
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerName($customerName)
    {
        $this->_customerName = $customerName;
        return $this;
    }

    /**
     * Set customer Email
     *
     * @param string $customerName
     * @return $this
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->_customerEmail = $customerEmail;
        return $this;
    }

    /**
     * Clean data
     *
     * @return $this
     */
    public function clean()
    {
        $this->_customer = null;
        $this->_customerEmail = null;
        $this->_stockProducts = [];

        return $this;
    }

    /**
     * Add product (back in stock) to collection
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function addStockProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->_stockProducts[$product->getId()] = $product;
        return $this;
    }

    /**
     * Retrieve stock block
     *
     * @return \Bss\ProductStockAlert\Block\Email\Stock
     */
    protected function _getStockBlock()
    {
        if ($this->_stockBlock === null) {
            $this->_stockBlock = $this->_productAlertData->createBlock('Bss\ProductStockAlert\Block\Email\Stock');
        }
        return $this->_stockBlock;
    }

    /**
     * Send customer email
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function send()
    {
        if ($this->_website === null || $this->_customerName === null) {
            return false;
        }
        if ($this->_type == 'stock' && count($this->_stockProducts) == 0) {
            return false;
        }
        if (!$this->_website->getDefaultGroup() || !$this->_website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }

        $store = $this->_website->getDefaultStore();

        $storeId = $store->getId();

        if ($this->_type == 'stock' && !$this->_scopeConfig->getValue(
            self::XML_PATH_EMAIL_STOCK_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        )
        ) {
            return false;
        }

        if ($this->_type != 'stock') {
            return false;
        }
        
        $this->_appEmulation->startEnvironmentEmulation($storeId);

        $this->_getStockBlock()->setStore($store)->reset();
        foreach ($this->_stockProducts as $product) {
            $this->_getStockBlock()->addProduct($product);
        }
        $block = $this->_getStockBlock();
        $templateId = $this->_scopeConfig->getValue(
            self::XML_PATH_EMAIL_STOCK_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $alertGrid = $this->_appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_FRONTEND,
            [$block, 'toHtml']
        );
        
        $this->_appEmulation->stopEnvironmentEmulation();

        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $templateId
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            [
                'customerName' => $this->_customerName,
                'alertGrid' => $alertGrid,
            ]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            )
        )->addTo(
            $this->_customerEmail,
            $this->_customerName
        )->getTransport();

        $transport->sendMessage();

        return true;
    }
}
