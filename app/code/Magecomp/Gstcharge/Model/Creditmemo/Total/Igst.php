<?php

namespace Magecomp\Gstcharge\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magento\Sales\Model\Order\Creditmemo;
use Magecomp\Gstcharge\Helper\Data as GstHelper;

class Igst extends AbstractTotal
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
        $amount = $creditmemo->getOrder()->getIgstCharge();
        $creditmemo->setIgstCharge($amount);

		if($this->helperData->getGstTaxType())
		{
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getIgstCharge());
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getIgstCharge());
		}else{
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal());
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() );
		}
        return $this;
    }
}
