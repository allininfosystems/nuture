<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Helper;

use Mirasvit\Rewards\Model\ResourceModel\Notification\Rule\CollectionFactory;

class Purchase extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Rewards\Model\PurchaseFactory
     */
    protected $purchaseFactory;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $cartFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Purchase\CollectionFactory
     */
    protected $purchaseCollectionFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Model\PurchaseFactory                          $purchaseFactory
     * @param \Magento\Checkout\Model\CartFactory                              $cartFactory
     * @param \Mirasvit\Rewards\Model\ResourceModel\Purchase\CollectionFactory $purchaseCollectionFactory
     * @param \Magento\Framework\App\Helper\Context                            $context
     */
    public function __construct(
        \Mirasvit\Rewards\Model\PurchaseFactory $purchaseFactory,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Mirasvit\Rewards\Model\ResourceModel\Purchase\CollectionFactory $purchaseCollectionFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->purchaseFactory = $purchaseFactory;
        $this->cartFactory = $cartFactory;
        $this->purchaseCollectionFactory = $purchaseCollectionFactory;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @param int|\Magento\Quote\Model\Quote $quoteId
     *
     * @return bool|\Mirasvit\Rewards\Model\Purchase
     */
    public function getByQuote($quoteId)
    {
        $quote = false;
        if (is_object($quoteId)) {
            $quote = $quoteId;
            $quoteId = $quote->getId();
        }
        if (!$quoteId) {
            return false;
        }
        $collection = $this->purchaseCollectionFactory->create()
                        ->addFieldToFilter('quote_id', $quoteId);
        if ($collection->count()) {
            $purchase = $collection->getFirstItem();
            if ($quote) {
                $purchase->setQuote($quote);
            }
        } else {
            $purchase = $this->purchaseFactory->create()->setQuoteId($quoteId);
            if ($quote) {
                $purchase->setQuote($quote);
            }
            $purchase->save();
        }

        return $purchase;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool|\Mirasvit\Rewards\Model\Purchase
     */
    public function getByOrder($order)
    {
        if (!$purchase = $this->getByQuote($order->getQuoteId())) {
            return false;
        }
        if (!$purchase->getOrderId()) {
            $purchase->setOrderId($order->getId())->save();
        }

        return $purchase;
    }

    /**
     * @return bool|\Mirasvit\Rewards\Model\Purchase
     */
    public function getPurchase()
    {
        $quote = $this->cartFactory->create()->getQuote();

        return $this->getByQuote($quote);
    }
}
