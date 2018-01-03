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
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Shopbybrand\Controller\Category;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Shopbybrand\Helper\Data;

/**
 * Class View
 *
 * @package Mageplaza\Shopbybrand\Controller\Category
 */
class View extends Action
{
    /**
     * @type \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @type \Mageplaza\Shopbybrand\Helper\Data
     */
    protected $helper;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     */
    public function __construct(Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory, Data $helper
    ) {
        $this->helper = $helper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        $id = $this->getRequest()->getParam('cat_id');

        return (
            $id && $this->helper->getShopByBrandConfig('sidebar/category_brand/enable') && $this->helper->getGeneralConfig('enabled')) ? $this->resultPageFactory->create()
            : $this->resultForwardFactory->create()->forward('noroute');
    }
}
