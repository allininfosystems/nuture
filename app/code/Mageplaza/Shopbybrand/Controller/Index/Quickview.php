<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Shopbybrand\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Session;


use Mageplaza\Shopbybrand\Model\BrandFactory;
use Mageplaza\Shopbybrand\Helper\Data as Helper;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;

class Quickview extends Action
{
    public $resultPageFactory;
    public $accountManagement;
    public $customerUrl;
    public $session;
    public $storeManager;

    protected $helper;
    protected $_brandFactory;
    protected $categoryRepository;
    protected $_coreRegistry;
    protected $_jsonHelper;
    protected $resultForwardFactory;

    public function __construct(Context $context,
        StoreManagerInterface $storeManager, PageFactory $resultPageFactory,
        AccountManagementInterface $accountManagement, CustomerUrl $customerUrl,

        BrandFactory $brandFactory, Helper $helper,
        CategoryRepositoryInterface $categoryRepository, Registry $coreRegistry,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,

        Session $customerSession
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->accountManagement = $accountManagement;
        $this->customerUrl = $customerUrl;

        $this->_brandFactory = $brandFactory;
        $this->helper = $helper;
        $this->categoryRepository = $categoryRepository;
        $this->_coreRegistry = $coreRegistry;
        $this->_jsonHelper = $jsonHelper;
        $this->resultForwardFactory = $resultForwardFactory;

        $this->session = $customerSession;
    }

    public function execute()
    {
        $brand = $this->_initBrand();
        if ($brand) {
            $page = $this->resultPageFactory->create();
            $page->getConfig()->addBodyClass('page-products');

            if ($this->getRequest()->isAjax()) {

                $layout = $page->getLayout();
                $imageUrl = $this->helper->getBrandImageUrl($brand);
                $brand->setImage($imageUrl);
                $html = $layout->getBlock('quick.view.products')->toHtml();

                $result = ['product' => $html, 'brand' => $brand->getData(),
                           'status'  => 'ok'];

                return $this->getResponse()->representJson(
                    $this->_jsonHelper->jsonEncode($result)
                );
            }

            return $page;
        }
        return $this->resultForwardFactory->create()->forward('noroute');
    }

    /**
     * @return bool
     */
    protected function _initBrand()
    {
        $urlKey = $this->getRequest()->getParam(
            $this->helper->getAttributeCode()
        );
        if (!$urlKey) {
            return false;
        }

        $currentBrand = false;
        try {
            $brand = $this->_brandFactory->create()->getBrandCollection(
                null, ['main_table.option_id' => ['eq' => $urlKey]]
            )->getFirstItem();
            if ($brand) {

                $currentBrand = $brand;
            }

            $category = $this->categoryRepository->get(
                $this->storeManager->getStore()->getRootCategoryId()
            );
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_brand', $currentBrand);
        $this->_coreRegistry->register('current_category', $category);

        return $currentBrand;
    }
}