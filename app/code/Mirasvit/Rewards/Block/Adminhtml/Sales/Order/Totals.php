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



namespace Mirasvit\Rewards\Block\Adminhtml\Sales\Order;

class Totals extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    protected $rewardsPurchase;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\App\ResourceConnection        $resource
     * @param \Mirasvit\Rewards\Helper\Purchase                $rewardsPurchase
     * @param \Mirasvit\Rewards\Helper\Data                    $rewardsData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Sales\Helper\Admin                      $adminHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->rewardsPurchase = $rewardsPurchase;
        $this->rewardsData = $rewardsData;
        $this->context = $context;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Initialize totals object.
     *
     * @return $this
     */
    protected function initTotals()
    {
        parent::_initTotals();
        $order = $this->getOrder();
        if (!$purchase = $this->rewardsPurchase->getByOrder($order)) {
            return $this;
        }

        $orderId = $order->getId();
        $resource = $this->resource;
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('mst_rewards_transaction');

        $sum = $purchase->getSpendPoints();
        if ($sum) {
            $this->addTotalBefore(new \Magento\Framework\DataObject([
                'code' => 'spend',
                'value' => $sum,
                'label' => $this->rewardsData->____('%1 Spent', $this->rewardsData->getPointsName()),
                'is_formated' => true,
            ], ['discount']));
        }

        $sumActual = (int) $readConnection->fetchOne(
            "SELECT SUM(amount) FROM $table WHERE code='order_earn-{$orderId}'"
        );
        $sum = $purchase->getEarnPoints();
        $pending = '';
        if ($sumActual == 0) {
            $pending = ' (pending)';
        }
        /** @var \Magento\Sales\Block\Adminhtml\Order\Totals $block */
        $block = $this->getParentBlock();
        if ($sum) {
            $block->addTotal(new \Magento\Framework\DataObject([
                'code'        => 'earn',
                'value'       => $sum,
                'label'       => $this->rewardsData->____('%1 Earned'.$pending, $this->rewardsData->getPointsName()),
                'is_formated' => true,
                'area'        => $this->getDisplayArea(),
                'strong'      => $this->getStrong(),
            ]), 'grand_total');
        }
        $purchase = $this->rewardsPurchase->getByQuote($order->getQuoteId());
        $sum = $purchase->getSpendAmount();
        if ($sum) {
            $block->addTotal(new \Magento\Framework\DataObject([
                'code'        => 'spent',
                'value'       => $this->rewardsData->formatCurrency($sum),
                'label'       => $this->rewardsData->____(
                    '%1 %2 Spent', $purchase->getSpendPoints(), $this->rewardsData->getPointsName()
                ),
                'is_formated' => true,
                'area'        => $this->getDisplayArea(),
                'strong'      => $this->getStrong(),
            ]), 'grand_total');
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * @return float
     */
    public function getRewardsPoints()
    {
        $total = $this->getParentBlock()->getTotal('earn');
        if (!$total) {
            $total = $this->getParentBlock()->getTotal('spent');
        }

        return $total->getValue();
    }

    /**
     * @return string
     */
    public function getRewardsLabel()
    {
        $total = $this->getParentBlock()->getTotal('earn');
        if (!$total) {
            $total = $this->getParentBlock()->getTotal('spent');
        }

        return $total->getLabel();
    }
}
