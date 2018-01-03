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
namespace Bss\ProductStockAlert\Controller\Add;

use Bss\ProductStockAlert\Controller\Add as AddController;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Stock extends AddController
{
    protected $productRepository;
    protected $modelStock;
    protected $store;
    protected $customer;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Bss\ProductStockAlert\Model\Stock $modelStock
     * @param \Magento\Store\Model\StoreManagerInterface $store
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        \Bss\ProductStockAlert\Model\Stock $modelStock,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Customer\Model\Customer $customer,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
        $this->modelStock = $modelStock;
        $this->store = $store;
        $this->customer = $customer;
        parent::__construct($context, $customerSession);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $backUrl = $this->getRequest()->getParam(Action::PARAM_NAME_URL_ENCODED);
        $productId = (int)$this->getRequest()->getParam('product_id');
        $customerEmail = $this->getRequest()->getParam('stockalert_email');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$backUrl || !$productId || !$customerEmail) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        try {
            /* @var $product \Magento\Catalog\Model\Product */
            $product = $this->productRepository->getById($productId);
            $customerId = $this->customerSession->getCustomerId() ? $this->customerSession->getCustomerId() : 0;
            $customerData = $this->customer->load($customerId);
            $customerName = $customerId ? $customerData->getFirstname() . " " . $customerData->getLastname() : "Guest";

            /** @var \Bss\ProductStockAlert\Model\Stock $model */
            $model = $this->modelStock
                ->setCustomerId($customerId)
                ->setCustomerEmail($customerEmail)
                ->setCustomerName($customerName)
                ->setProductSku($product->getSku())
                ->setProductId($product->getId())
                ->setWebsiteId(
                    $this->store->getStore()->getWebsiteId()
                );
            $model->save();
            if($customerId == 0) {
                $notify = $this->customerSession->getNotifySubscription();
                $count = count($notify);
                if ($count) {
                    $notify[$product->getId()] = ["email" => $customerEmail, "website" => $this->store->getStore()->getWebsiteId()];
                } else {
                    $notify = [];
                    $notify[$product->getId()] = ["email" => $customerEmail, "website" => $this->store->getStore()->getWebsiteId()];
                }
                $this->customerSession->setNotifySubscription($notify);
            }
            $this->messageManager->addSuccess(__('Alert subscription has been saved.'));
        } catch (NoSuchEntityException $noEntityException) {
            $this->messageManager->addError(__('There are not enough parameters.'));
            $resultRedirect->setUrl($backUrl);
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t update the alert subscription right now.'));
        }
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }
}
