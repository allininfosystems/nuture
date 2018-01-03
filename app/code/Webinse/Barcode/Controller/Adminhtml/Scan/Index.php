<?php
namespace Webinse\Barcode\Controller\Adminhtml\Scan;

use Magento\Backend\App\Action;

class Index extends Action
{
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Scan Barcodes'));
        $this->_view->renderLayout();
    }
}