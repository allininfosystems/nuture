<?php
namespace Magestore\Rewardpoints\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveBlock implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getLayout();
        $block = $layout->getBlock('my-account-link');
        if ($block) {
            $remove = $this->_scopeConfig->getValue(
                'rewardpoints/general/enable',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if ($remove) {
                $layout->unsetElement('my-account-link');
            }
        }
    }
}