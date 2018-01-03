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
namespace Bss\ProductStockAlert\Controller\Adminhtml\Index;
 
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Bss\ProductStockAlert\Model\ResourceModel\Stock\CollectionFactory;
use Bss\ProductStockAlert\Model\Stock as Stock;
 
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.
     *
     * @var Filter
     */
    protected $filter;
 
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
 
    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    protected $model;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Stock $model
    ) {
    
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->model = $model;
        parent::__construct($context);
    }
 
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $recordDeleted = 0;
        foreach ($collection->getItems() as $auctionProduct) {
            $this->delete($auctionProduct);
            $recordDeleted++;
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $recordDeleted)
        );
 
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    protected function delete($auctionProduct)
    {
        $this->model->setId($auctionProduct->getId());
        $this->model->delete();
    }
 
    /**
     * Check delete Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_ProductStockAlert::manage');
    }
}
