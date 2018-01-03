<?php
namespace Magecomp\Gstcharge\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;

class Buyergstnumberinorderview implements ObserverInterface
{
    protected $objectManager;
	protected $template;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager,Template $template)
    {
        $this->objectManager = $objectManager;
		$this->template = $template;
    }

    public function execute(EventObserver $observer)
    {
		
		
        if($observer->getElementName() == 'order_shipping_view' ) {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
			$formattedDate = __('N/A');
            if($order->getBuyerGstNumber() != '') {
                $formattedDate = $order->getBuyerGstNumber() ;
            }
			$deliveryDateBlock = $this->template;
			$deliveryDateBlock->setBuyerGstNumber($formattedDate);
		   
			$deliveryDateBlock->setTemplate('Magecomp_Gstcharge::buyergstnumber.phtml');
			$html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();
			$observer->getTransport()->setOutput($html);
        }
		 if($observer->getElementName() ==  'sales_invoice_view') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $invoice = $orderShippingViewBlock->getInvoice();
          $formattedDate = __('N/A');
            if($invoice->getBuyerGstNumber() != '') {
                $formattedDate = $invoice->getBuyerGstNumber() ;
            } 
			$deliveryDateBlock = $this->template;
			$deliveryDateBlock->setBuyerGstNumber($formattedDate);
		   
			$deliveryDateBlock->setTemplate('Magecomp_Gstcharge::buyergstnumber.phtml');
			$html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();
			$observer->getTransport()->setOutput($html);
        }
		 if($observer->getElementName() ==  'sales_creditmemo_view') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $invoice = $orderShippingViewBlock->getCreditmemo();
          $formattedDate = __('N/A');
            if($invoice->getBuyerGstNumber() != '') {
                $formattedDate = $invoice->getBuyerGstNumber() ;
            } 
			$deliveryDateBlock = $this->template;
			$deliveryDateBlock->setBuyerGstNumber($formattedDate);
		   
			$deliveryDateBlock->setTemplate('Magecomp_Gstcharge::buyergstnumber.phtml');
			$html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();
			$observer->getTransport()->setOutput($html);
        }
		
		
    }
}