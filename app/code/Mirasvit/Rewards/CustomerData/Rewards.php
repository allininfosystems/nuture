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


namespace Mirasvit\Rewards\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Mirasvit\Rewards\Helper\Balance;
use Magento\Customer\Model\Session;
use Mirasvit\Rewards\Helper\Data as DataHelper;
use Mirasvit\Rewards\Model\Config;

class Rewards implements SectionSourceInterface
{
    public function __construct(
        Balance $balance,
        Session $customerSession,
        DataHelper $dataHelper,
        Config $config
    ) {
        $this->balance         = $balance;
        $this->customerSession = $customerSession;
        $this->dataHelper      = $dataHelper;
        $this->config          = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $amount = 0;
        if ($balance = $this->getBalance()) {
            $amount = $this->dataHelper->formatPointsWithCutUnitName($balance);
        }

        return [
            'amount'    => $amount,
            'isVisible' => (bool)$this->config->getDisplayOptionsIsShowPointsOnFrontend(),
        ];
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->balance->getBalancePoints($this->customerSession->getCustomer());
    }
}
