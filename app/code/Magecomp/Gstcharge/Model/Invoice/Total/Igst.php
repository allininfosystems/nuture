<?php
namespace Magecomp\Gstcharge\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Sales\Model\Order\Invoice;

class Igst extends AbstractTotal
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
        $invoice->getCgstCharge(0);
		 $invoice->getSgstCharge(0);
        $amount = $invoice->getOrder()->getIgstCharge();
        $invoice->setIgstCharge($amount);

		if($this->helperData->getGstTaxType())
		{
	        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getIgstCharge());
	        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getIgstCharge());
		}
		else{
	        $invoice->setGrandTotal($invoice->getGrandTotal() );
	        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal());
		}
        return $this;
    }
}
