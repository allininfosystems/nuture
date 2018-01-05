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


namespace Mirasvit\Rewards\Api;

/**
 * Interface for rewards quote calculation
 * @api
 */
interface RewardsInterface
{
    /**
     * @param mixed $shippingCarrier //we need mixed type here
     * @param mixed $shippingMethod  //we need mixed type here
     *
     * @return \Mirasvit\Rewards\Api\Data\RewardsInterface
     */
    public function update($shippingCarrier = '', $shippingMethod = '');
}
