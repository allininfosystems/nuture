<?php

namespace Magecomp\Gstcharge\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magento\Sales\Model\Order\Creditmemo;
use Magecomp\Gstcharge\Helper\Data as GstHelper;

class Sgst extends AbstractTotal
{
     protected $helperData;
	 public function __construct(
     GstHelper $helperData)
    {
        $this->helperData = $helperData;
    }  
    public function collect(Creditmemo $creditmemo)
    {
        $amount = $creditmemo->getOrder()->getSgstCharge();
        $creditmemo->setSgstCharge($amount);
		if($this->helperData->getGstTaxType())
		{
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getSgstCharge());
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getSgstCharge());
		}
		else
		{
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal());
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal());
		}
        return $this;
    }
}
