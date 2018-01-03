<?php

namespace Cminds\StockNotification\Observer;

use Cminds\StockNotification\Helper\SendMail;
use Cminds\StockNotification\Model\ResourceModel\StockNotification\CollectionFactory
    as StockNotificationCollectionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductIsSalable implements ObserverInterface
{
    /**
     * SendMail object.
     *
     * @var SendMail
     */
    private $sendMail;

    /**
     * Stock notification collection factory object.
     *
     * @var StockNotificationCollectionFactory
     */
    private $stockNotificationCollectionFactory;

    /**
     * ProductIsSalable constructor
     *
     * @param SendMail                           $sendMail
     * @param StockNotificationCollectionFactory $stockNotificationCollectionFactory
     */
    public function __construct(
        SendMail $sendMail,
        StockNotificationCollectionFactory $stockNotificationCollectionFactory
    ) {

        $this->stockNotificationCollectionFactory = $stockNotificationCollectionFactory;
        $this->sendMail = $sendMail;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $currentProductId = $observer = $observer->getEvent()->getData('product_id');

        $this->notifySubscribers($currentProductId);
    }

    /**
     * Notify subscribers on the list
     *
     * @param $productId
     */
    private function notifySubscribers($productId)
    {
        if ($productId !== null) {
            $collection = $this->stockNotificationCollectionFactory->create();
            $collection
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('notified', false);

            if ($collection->getSize()) {
                $collection->each(function ($item, $arguments) {
                    $this->sendMail->send(
                        $item->getData('email'),
                        $arguments[0]
                    );

                    $item
                        ->setData('notified', true)
                        ->save();
                }, [$productId]);
            }
        }
    }
}
