<?php
namespace Magestore\Rewardpoints\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class BlockToHtmlAfter implements ObserverInterface
{

    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {

        $enableExtension = $this->_scopeConfig->getValue(
            'rewardpoints/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if(!$enableExtension){
            return $this;
        }

        if ($observer['element_name']=='checkout.cart.coupon') {
            $data = $observer['transport']->getData('output');
            $creditFormHtml = $observer['layout']->createBlock('Magestore\Rewardpoints\Block\Checkout\Cart\Point')->setTemplate('Magestore_Rewardpoints::rewardpoints/checkout/cart/point.phtml')
                ->toHtml();
            $observer['transport']->setData('output', $data.$creditFormHtml);
        };
    }
}