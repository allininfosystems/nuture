<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Block\Buttons;

class AbstractButtons extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->config          = $config;
        $this->registry        = $registry;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->productFactory  = $productFactory;
        $this->context         = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return \Mirasvit\Rewards\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        $locale = $this->context->getStoreManager()->getStore()->getLocaleCode();
        if (!$locale) {
            $locale = 'en';
        }

        return $locale;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        if ($product = $this->registry->registry('current_product')) {
            $url = $this->getProductUrl($product);
        } elseif ($category = $this->registry->registry('current_category')) {
            $url = $category->getUrl();
        } elseif ($id = $this->getRequest()->getParam('id')) {
            $product = $this->productFactory->create()->load($id);
            $url = $this->getProductUrl($product);
        }

        $pos = strpos($url, '?__SID');
        if ($pos !== false) {
            $url = substr($url, 0, $pos);
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        $name = '';
        if ($product = $this->registry->registry('current_product')) {
            $name = $product->getName();
        } elseif ($id = $this->getRequest()->getParam('id')) {
            $product = $this->productFactory->create()->load($id);
            $name = $product->getName();
        }

        return $name;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    private function getProductUrl($product)
    {
        if ($this->context->getStoreManager()->getStore()->isUseStoreInUrl()) {
            $url = $product->getUrlInStore(['_use_rewrite' => (bool)$product->getUrlKey()]);
        } else {
            $url = $product->getProductUrl();
        }

        return $url;
    }

    /**
     * @param string $url
     * @return string
     */
    public function getCurrentEncodedUrl($url)
    {
        return urlencode($url);
    }

    /**
     * @return $this
     */
    public function _getCustomer()
    {
        return $this->customerFactory->create()->load($this->customerSession->getCustomerId());
    }

    /**
     * @return bool
     */
    public function isAuthorized()
    {
        $customer = $this->_getCustomer();
        if ($customer && $customer->getId() > 0) {
            return true;
        }
    }
}
