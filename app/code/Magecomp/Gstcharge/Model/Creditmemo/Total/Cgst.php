<?php

namespace Magecomp\Gstcharge\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magento\Sales\Model\Order\Creditmemo;
use Magecomp\Gstcharge\Helper\Data as GstHelper;

class Cgst extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
	 protected $helperData;
	 public function __construct(
      GstHelper $helperData)
    {
        
        $this->helperData = $helperData;
    }  
    public function collect(Creditmemo $creditmemo)
    {
        $amount = $creditmemo->getOrder()->getCgstCharge();
        $creditmemo->setCgstCharge($amount);

		if($this->helperData->getGstTaxType())
		{
       	    $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getCgstCharge());
        	$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getCgstCharge());
		}
		else{
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal());
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal());
		}
        return $this;
    }
}
