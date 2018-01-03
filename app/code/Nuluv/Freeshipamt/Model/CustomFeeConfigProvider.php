<?php

namespace Nuluv\Freeshipamt\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class CustomFeeConfigProvider implements ConfigProviderInterface
{

    protected $dataHelper;

    protected $checkoutSession;

    protected $logger;

    public function __construct(
        \Nuluv\Freeshipamt\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger

    )
    {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    public function getConfig()
    {
        $customFeeConfig = [];
		
        $customFeeConfig['freeshipamt_label'] = $this->dataHelper->getFeeLabelFreeshipamt();

        return $customFeeConfig;
    }

}
