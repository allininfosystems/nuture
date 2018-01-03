<?php
namespace Webinse\Barcode\Controller\Adminhtml\Generator;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\ProductFactory;

class Generate extends Action
{
    protected $_modelProduct;

    public function __construct(Action\Context $context, ProductFactory $modelProduct)
    {
        parent::__construct($context);
        $this->_modelProduct = $modelProduct;
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Barcode generator'));
        $this->_view->renderLayout();

    }

    public function getProductId()
    {
        if ($product = $this->getRequest()->getParam('product')) {
            return $product;
        }
        return false;
    }
}