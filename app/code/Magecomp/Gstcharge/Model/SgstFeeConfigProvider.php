<?php
namespace Magecomp\Gstcharge\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Checkout\Model\Session;

class SgstFeeConfigProvider implements ConfigProviderInterface
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
			$GstchargeConfig['sgst_label'] = 'SGST';
			$GstchargeConfig['sgst_charge'] = $this->dataHelper->getSgstCharge();
			$GstchargeConfig['show_hide_sgst_block'] = ($enabled) ? true : false;
			$GstchargeConfig['show_hide_sgst_shipblock'] = ($enabled) ? true : false;
		}
        return $GstchargeConfig;
    }
}
