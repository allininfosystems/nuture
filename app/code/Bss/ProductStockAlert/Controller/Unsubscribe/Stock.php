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
namespace Bss\ProductStockAlert\Controller\Unsubscribe;

use Bss\ProductStockAlert\Controller\Unsubscribe as UnsubscribeController;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Stock extends UnsubscribeController
{
    protected $productRepository;

    protected $modelStock;

    protected $store;
    
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        \Bss\ProductStockAlert\Model\Stock $modelStock,
        \Magento\Store\Model\StoreManagerInterface $store,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
        $this->modelStock = $modelStock;
        $this->store = $store;
        parent::__construct($context, $customerSession);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('product_id');
        $backurl = (int)$this->getRequest()->getParam('backurl');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$productId) {
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }

        try {
            $product = $this->productRepository->getById($productId);
            if (!$product->isVisibleInCatalog()) {
                throw new NoSuchEntityException();
            }
            if($this->customerSession->getCustomerId()) {
                $model = $this->modelStock
                    ->setCustomerId($this->customerSession->getCustomerId())
                    ->setProductId($product->getId())
                    ->setWebsiteId(
                        $this->store
                            ->getStore()
                            ->getWebsiteId()
                    )
                    ->loadByParam();
            }else{
                $notify = $this->customerSession->getNotifySubscription();
                $email = $notify[$product->getId()]['email'];
                $model = $this->modelStock
                    ->setCustomerEmail($email)
                    ->setProductId($product->getId())
                    ->setWebsiteId(
                        $this->store
                            ->getStore()
                            ->getWebsiteId()
                    )
                    ->loadByParamGuest();
                unset($notify[$product->getId()]);
                $this->customerSession->setNotifySubscription($notify);
            }
            if ($model->getAlertStockId()) {
                $model->delete();
            }
            $this->messageManager->addSuccess(__('You will no longer receive stock alerts for this product.'));
        } catch (NoSuchEntityException $noEntityException) {
            $this->messageManager->addError(__('The product was not found.'));
            $resultRedirect->setPath('customer/account/');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t update the alert subscription right now.'));
        }
        if($backurl){
            $resultRedirect->setUrl($this->_url->getUrl("productstockalert"));
            return $resultRedirect;
        }
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }
}
