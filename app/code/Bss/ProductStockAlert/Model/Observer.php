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
 * ProductAlert observer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Observer
{
    /**
     * Website collection array
     *
     * @var array
     */
    protected $websites;

    /**
     * Warning (exception) errors array
     *
     * @var array
     */
    protected $errors = [];

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Bss\ProductStockAlert\Model\EmailFactory
     */
    protected $emailFactory;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerHelper;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Bss\ProductStockAlert\Model\Stock
     */

    protected $modelstock;

    /**
     * @var \Magento\CatalogInventory\Model\StockRegistry
     */

    protected $stockRegistry;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
     * @param \Bss\ProductStockAlert\Model\Stock $modelstock
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Bss\ProductStockAlert\Model\EmailFactory $emailFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Bss\ProductStockAlert\Helper\Data $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Bss\ProductStockAlert\Model\Stock $modelstock,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Bss\ProductStockAlert\Model\EmailFactory $emailFactory,
        \Magento\Customer\Helper\View $customerHelper,
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->helper = $helper;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->dateFactory = $dateFactory;
        $this->transportBuilder = $transportBuilder;
        $this->emailFactory = $emailFactory;
        $this->_customerHelper = $customerHelper;
        $this->stockRegistry = $stockRegistry;
        $this->modelstock = $modelstock;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Retrieve website collection array
     *
     * @return array
     */
    protected function _getWebsites()
    {
        if ($this->websites === null) {
            try {
                $this->websites = $this->helper->getWebsites();
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }
        return $this->websites;
    }

    /**
     * Process stock emails
     *
     * @param \Bss\ProductStockAlert\Model\Email $email
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _processStock(\Bss\ProductStockAlert\Model\Email $email)
    {
        $email->setType('stock');
        $listEmailProduct = $customerNames = [];
        foreach ($this->_getWebsites() as $website) {
            /* @var $website \Magento\Store\Model\Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            if (!$this->helper->isStockAlertAllowed()) {
                continue;
            }
            try {
                $collection = $this->modelstock->getCollection()->addFieldToFilter(
                    "website_id",
                    ['eq' => $website->getId()]                    
                );
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
                return $this;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                try {
                    $product = $this->productRepository->getById(
                        $alert->getProductId(),
                        false,
                        $website->getDefaultStore()->getId()
                    );

                    if (!(
                        $product->getTypeId() == "bundled"
                        || $product->getTypeId() == "configurable"
                        || $product->getTypeId() == "grouped"
                    )) {
                        $productStock = $this->stockRegistry->getStockItem($alert->getProductId());
                        if($this->helper->getQtySendMail() && $this->helper->getQtySendMail() > $productStock->getQty())
                            continue;
                    }

                    if($this->helper->getLimitCount() && $this->helper->getLimitCount() <= $alert->getSendCount())
                        continue;
                    
                    if ($product->isSalable() && $alert->getStatus() == 0) {
                        if($alert->getCustomerId() == 0)
                        {
                            $customerNames[$alert->getCustomerEmail()] = "Guest";
                        }else{
                            $customer = $this->customerRepository->getById($alert->getCustomerId());
                            $customerNames[$alert->getCustomerEmail()]= $this->_customerHelper->getCustomerName($customer);
                        }
                    
                        $listEmailProduct[$alert->getCustomerEmail()][] = $alert->getProductId();

                        $this->modelstock->load($alert->getAlertStockId());
                        $this->modelstock->setSendDate($this->dateFactory->create()->gmtDate());
                        $this->modelstock->setSendCount($this->modelstock->getSendCount() + 1);
                        $this->modelstock->setStatus(1);
                        $this->modelstock->save();
                    }else if(!$product->isSalable() && $alert->getStatus() == 1) {
                        $this->modelstock->load($alert->getAlertStockId());
                        $this->modelstock->setStatus(0);
                        $this->modelstock->save();
                    }

                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        }

        if(empty($listEmailProduct))
            return $this;

        foreach($listEmailProduct as $emailSend => $productIds)
        {
            try {
                foreach($productIds as $productId){
                    $product = $this->productRepository->getById(
                        $productId,
                        false,
                        $website->getDefaultStore()->getId()
                    );
                    
                    if ($product->isSalable()) {
                        $email->addStockProduct($product);
                        $email->setCustomerName($customerNames[$emailSend]);
                    }
                }
                $email->setCustomerEmail($emailSend);
                $email->send();
                $email->clean();
            } catch (\Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        return $this;
    }

    /**
     * Send email to administrator if error
     *
     * @return $this
     */
    protected function _sendErrorEmail()
    {
        $count = count($this->errors);
        if ($count) {
            if (!$this->helper->getEmailErrorTemplate()) {
                return $this;
            }

            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->helper->getEmailErrorTemplate()
            )->setTemplateOptions(
                [
                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )->setTemplateVars(
                ['warnings' => join("\n", $this->errors)]
            )->setFrom(
                $this->helper->getEmailErrorIdentity()
            )->addTo(
                $this->helper->getEmailErrorRecipient()
            )->getTransport();

            $transport->sendMessage();
            
            $this->inlineTranslation->resume();
            $this->errors[] = [];
        }
        return $this;
    }

    /**
     * Run process send product alerts
     *
     * @return $this
     */
    public function process()
    {
        /* @var $email \Bss\ProductStockAlert\Model\Email */
        $email = $this->emailFactory->create();
        $this->_processStock($email);
        $this->_sendErrorEmail();

        return $this;
    }
}
