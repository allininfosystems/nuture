<?php

namespace Cminds\StockNotification\Observer;

use Magento\Framework\Event\Manager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class ProductObserverAfterSave implements ObserverInterface
{
    /**
     * Registry object
     *
     * @var Registry
     */
    private $registry;

    /**
     * EventManager object
     *
     * @var Manager
     */
    private $eventManager;

    /**
     * ProductObserverAfterSave constructor
     *
     * @param Registry $registry
     * @param Manager  $eventManager
     */
    public function __construct(
        Registry $registry,
        Manager $eventManager
    ) {

        $this->registry = $registry;
        $this->eventManager = $eventManager;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $stockData = $observer->getEvent()->getData('product')->getStockData();
        $currentStatus = (bool)$stockData['is_in_stock'];

        if ($currentStatus === true
            && $currentStatus !== $this->checkPreviousProductStatus()
        ) {
            $this->eventManager->dispatch(
                'cminds_stocknotification_product_is_salable',
                ['product_id' => $stockData['product_id']]
            );
        }
    }

    /**
     * Check previous status of product
     *
     * @return bool
     */
    private function checkPreviousProductStatus()
    {
        return (bool)$this->registry->registry('cminds_stocknotification_product_is_salable_before');
    }
}
