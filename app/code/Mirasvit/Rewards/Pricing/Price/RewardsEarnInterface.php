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


namespace Mirasvit\Rewards\Pricing\Price;

/**
 * Special price interface
 */
interface RewardsEarnInterface
{
    /**
     * Get Minimal Price Amount
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMinimalPrice();

    /**
     * Get Maximal Price Amount
     *
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaximalPrice();
}
