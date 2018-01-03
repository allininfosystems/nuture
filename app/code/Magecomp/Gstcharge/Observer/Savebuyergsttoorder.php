<?php
namespace Magecomp\Gstcharge\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Model\QuoteRepository;

class Savebuyergsttoorder implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	protected $quoteRepository;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(ObjectManagerInterface $objectmanager, QuoteRepository $quoteRepository)
    {
        $this->_objectManager = $objectmanager;
		$this->quoteRepository = $quoteRepository;
    }

    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        $quoteRepository = $this->quoteRepository;
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $quoteRepository->get($order->getQuoteId());
        $order->setBuyerGstNumber( $quote->getBuyerGstNumber() );

        return $this;
    }
}