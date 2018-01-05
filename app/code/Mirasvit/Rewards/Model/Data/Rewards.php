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


namespace Mirasvit\Rewards\Model\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use Mirasvit\Rewards\Api\Data\RewardsInterface;

class Rewards extends AbstractExtensibleModel implements RewardsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChechoutRewardsIsShow()
    {
        return $this->getData(self::ISSHOW);
    }

    /**
     * {@inheritdoc}
     */
    public function setChechoutRewardsIsShow($isShow)
    {
        return $this->setData(self::ISSHOW, $isShow);
    }

    /**
     * {@inheritdoc}
     */
    public function getChechoutRewardsPoints()
    {
        return $this->getData(self::POINTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setChechoutRewardsPoints($points)
    {
        return $this->setData(self::POINTS, $points);
    }

    /**
     * {@inheritdoc}
     */
    public function getChechoutRewardsPointsMax()
    {
        return $this->getData(self::POINTS_MAX);
    }

    /**
     * {@inheritdoc}
     */
    public function setChechoutRewardsPointsMax($points)
    {
        return $this->setData(self::POINTS_MAX, $points);
    }

    /**
     * {@inheritdoc}
     */
    public function getChechoutRewardsPointsSpend()
    {
        return $this->getData(self::POINTS_SPEND);
    }

    /**
     * {@inheritdoc}
     */
    public function setChechoutRewardsPointsSpend($points)
    {
        return $this->setData(self::POINTS_SPEND, $points);
    }

    /**
     * {@inheritdoc}
     */
    public function getChechoutRewardsPointsAvailble()
    {
        return $this->getData(self::POINTS_AVAILABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setChechoutRewardsPointsAvailble($points)
    {
        return $this->setData(self::POINTS_AVAILABLE, $points);
    }

    /**
     * {@inheritdoc}
     */
    public function getChechoutRewardsPointsUsed()
    {
        return $this->getData(self::POINTS_USED);
    }

    /**
     * {@inheritdoc}
     */
    public function setChechoutRewardsPointsUsed($points)
    {
        return $this->setData(self::POINTS_USED, $points);
    }
}