<?php
namespace Magecomp\Gstcharge\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Checkout\Model\Session;

class ShippingIgstFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magecomp\Gstcharge\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @param \Magecomp\Gstcharge\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        GstHelper $dataHelper,
        Session $checkoutSession
    )
    {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
		$GstchargeConfig = [];
        $enabled = $this->dataHelper->isModuleEnabled();
		
		if($enabled)
		{
			$quote = $this->checkoutSession->getQuote();
			$address = $quote->getShippingAddress();
			$GstchargeConfig['shipping_igst_label'] = 'Shipping IGST';
			$GstchargeConfig['shipping_igst_charge'] = $address->getShippingIgstCharge();
			$GstchargeConfig['show_hide_Shipping_Igstcharge_block'] = ($enabled) ? true : false;
			$GstchargeConfig['show_hide_Shipping_Igstcharge_shipblock'] = ($enabled) ? true : false;
		}
        return $GstchargeConfig;
    }
}
