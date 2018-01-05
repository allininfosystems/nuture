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


namespace Mirasvit\Rewards\Model\Checkout;

use \Magento\Framework\DataObject;

/**
 * Class TotalsInformationManagement
 */
class Rewards implements \Mirasvit\Rewards\Api\RewardsInterface
{
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Mirasvit\Rewards\Model\Data\RewardsFactory $rewardsDataFactory,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase
    ) {
        $this->request            = $request;
        $this->rewardsDataFactory = $rewardsDataFactory;
        $this->rewardsData        = $rewardsData;
        $this->rewardsPurchase    = $rewardsPurchase;
    }

    /**
     * {@inheritdoc}
     */
    public function update($shippingCarrier = '', $shippingMethod = '')
    {
        $result = [];
        $result['chechoutRewardsIsShow']         = 0;
        $result['chechoutRewardsPoints']         = 0;
        $result['chechoutRewardsPointsMax']      = 0;
        $result['chechoutRewardsPointsSpend']    = 0;
        $result['chechoutRewardsPointsAvailble'] = 0;
        if (($purchase = $this->rewardsPurchase->getPurchase()) && $purchase->getQuote()->getCustomerId()) {
            if ($purchase->getQuote()->getShippingAddress()->getCountryId() !== null) {
                $purchase->getQuote()->setCartShippingCarrier($shippingCarrier);
                $purchase->getQuote()->setCartShippingMethod($shippingMethod);
            }
            $purchase->refreshPointsNumber(true);
            if ($purchase->getEarnPoints()) {
                $result['chechoutRewardsPoints'] = $this->rewardsData->formatPoints($purchase->getEarnPoints());
            }
            if ($point = $purchase->getSpendPoints()) {
                $result['chechoutRewardsPointsSpend'] = $this->rewardsData->formatPoints($point);
                $result['chechoutRewardsPointsUsed']  = $point;
            }
            $quote = $purchase->getQuote();
            $result['chechoutRewardsPointsAvailble'] = $this->rewardsData->formatPoints(
                $purchase->getCustomerBalancePoints($quote->getCustomerId())
            );
            $result['chechoutRewardsPointsMax'] = $purchase->getMaxPointsNumberToSpent();
            $result['chechoutRewardsIsShow']    = (bool)$result['chechoutRewardsPointsMax'];
        }

        $rewards = $this->rewardsDataFactory->create();
        $rewards->setData($result);

        return $rewards;
    }
}
