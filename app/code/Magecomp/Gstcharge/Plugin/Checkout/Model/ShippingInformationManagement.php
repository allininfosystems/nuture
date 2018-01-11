<?php
namespace Magecomp\Gstcharge\Plugin\Checkout\Model;

use Magento\Quote\Model\QuoteRepository;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Checkout\Model\ShippingInformationManagement as CheckouShippingInformationManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;

class ShippingInformationManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $dataHelper;
	protected $totalsCollector;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magecomp\Gstcharge\Helper\Data $dataHelper
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        GstHelper $dataHelper,
		TotalsCollector $totalsCollector
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->dataHelper = $dataHelper;
		$this->totalsCollector = $totalsCollector;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        CheckouShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    )
    {
		$buyerGstNumber = "";
		
		 $extAttributes = $addressInformation->getExtensionAttributes();
		 try{
			if(sizeof($extAttributes)){
		 $buyerGstNumber = $extAttributes->getBuyerGstNumber();
			}
		 }catch (\Exception $e) {
		}
		 
		 $quote = $this->quoteRepository->getActive($cartId);
 		
		 $quote->setBuyerGstNumber($buyerGstNumber);
       	
		$cgstfee = $this->dataHelper->getCgstCharge();
		if ($cgstfee){
			$cgstfee = $this->dataHelper->getCgstCharge();
		
			$quote->setCgstCharge($cgstfee);
			$quote->getShippingAddress()->setCgstCharge($cgstfee);
       	} else {
		   	$quote->setCgstCharge(NULL);
            $quote->getShippingAddress()->setCgstCharge(NULL);
       	}
		
		$sgstfee = $this->dataHelper->getSgstCharge();
		if ($sgstfee){
            $sgstfee = $this->dataHelper->getSgstCharge();
			$quote->setSgstCharge($sgstfee);
            $quote->getShippingAddress()->setSgstCharge($sgstfee);
		} else {
			$quote->setSgstCharge(NULL);
            $quote->getShippingAddress()->setSgstCharge(NULL);
        }
		
		$igstfee = $this->dataHelper->getIgstCharge();
        if ($igstfee) {
            $igstfee = $this->dataHelper->getIgstCharge();
			$quote->setIgstCharge($igstfee);
            $quote->getShippingAddress()->setIgstCharge($igstfee);				
        } else {
			$quote->setIgstCharge(NULL);
            $quote->getShippingAddress()->setIgstCharge(NULL);
        }
    }
}