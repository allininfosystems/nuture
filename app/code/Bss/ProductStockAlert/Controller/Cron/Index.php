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
namespace Bss\ProductStockAlert\Controller\Cron;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $observer;

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Bss\ProductStockAlert\Model\Observer $observer
    ) {
        parent::__construct($context);
        $this->observer = $observer;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->observer->process();
        // $this->messageManager->addSuccess(__('Done'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
