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


namespace Mirasvit\Rewards\Api\Data;

/**
 * Interface for rewards quote calculation
 * @api
 */
interface RewardsInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const ISSHOW = 'chechoutRewardsIsShow';
    const POINTS = 'chechoutRewardsPoints';
    const POINTS_MAX = 'chechoutRewardsPointsMax';
    const POINTS_SPEND = 'chechoutRewardsPointsSpend';
    const POINTS_AVAILABLE = 'chechoutRewardsPointsAvailble';
    const POINTS_USED = 'chechoutRewardsPointsUsed';

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getChechoutRewardsIsShow();

    /**
     * @param bool $isShow
     * @return $this
     */
    public function setChechoutRewardsIsShow($isShow);

    /**
     * @return string
     */
    public function getChechoutRewardsPoints();

    /**
     * @param string $points
     * @return $this
     */
    public function setChechoutRewardsPoints($points);

    /**
     * @return int
     */
    public function getChechoutRewardsPointsMax();

    /**
     * @param int $points
     * @return $this
     */
    public function setChechoutRewardsPointsMax($points);

    /**
     * @return string
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getChechoutRewardsPointsSpend();

    /**
     * @param string $points
     * @return $this
     */
    public function setChechoutRewardsPointsSpend($points);

    /**
     * @return string
     */
    public function getChechoutRewardsPointsAvailble();

    /**
     * @param string $points
     * @return $this
     */
    public function setChechoutRewardsPointsAvailble($points);

    /**
     * @return int
     */
    public function getChechoutRewardsPointsUsed();

    /**
     * @param int $points
     * @return $this
     */
    public function setChechoutRewardsPointsUsed($points);
}
