<?php
namespace Magecomp\Gstcharge\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Sales\Model\Order\Invoice;

class Shippingigst extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
	 protected $helperData;
	 public function __construct(
     GstHelper $helperData)
    {
        
        $this->helperData = $helperData;
    } 
    public function collect(Invoice $invoice)
    {
			
        $invoice->getShippingCgstCharge(0);
		$invoice->getShippingSgstCharge(0);
        $amount = $invoice->getOrder()->getShippingIgstCharge();
        $invoice->setShippingIgstCharge($amount);

		if($this->helperData->getShippingGstTaxType())
		{
	        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getShippingIgstCharge());
	        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getShippingIgstCharge());
		}
		else{
	        $invoice->setGrandTotal($invoice->getGrandTotal() );
	        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal());
		}
        return $this;
    }
}
