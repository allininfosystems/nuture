<?php

namespace Cminds\StockNotification\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class ProductObserverBeforeSave implements ObserverInterface
{
    /**
     * Registry object
     *
     * @var Registry
     */
    private $registry;

    /**
     * ProductObserverBeforeSave constructor
     *
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param Observer $observer
     *
     * @throws \RuntimeException
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $this->registry->register(
            'cminds_stocknotification_product_is_salable_before',
            $product->isSalable()
        );
    }
}
