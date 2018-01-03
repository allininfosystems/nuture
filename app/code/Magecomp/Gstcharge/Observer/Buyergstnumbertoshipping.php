<?php
namespace Magecomp\Gstcharge\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;

class Buyergstnumbertoshipping implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
	protected $template;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager,Template $template)
    {
        $this->objectManager = $objectManager;
		$this->template = $template;
    }

    public function execute(EventObserver $observer)
    {
        if($observer->getElementName() == 'sales.order.info') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
          
            if($order->getBuyerGstNumber() != '') {
                $formattedDate = $order->getBuyerGstNumber() ;
            } else {
                $formattedDate = __('N/A');
            }

            $deliveryDateBlock = $this->template;
            $deliveryDateBlock->setBuyerGstNumber($formattedDate);

            $deliveryDateBlock->setTemplate('Magecomp_Gstcharge::buyergstnumber.phtml');
            $html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();
            $observer->getTransport()->setOutput($html);
        }
    }
}