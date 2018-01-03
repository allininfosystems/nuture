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
namespace Bss\ProductStockAlert\Controller\Index;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $helper;
    protected $customerSession;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Bss\ProductStockAlert\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Display customer productstockalert
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->helper->isStockAlertAllowed() || !$this->customerSession->isLoggedIn()) {
            throw new NotFoundException(__('Page not found.'));
        }
        /** @var \Magento\Framework\View\Result\Page resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $resultPage;
    }
}
