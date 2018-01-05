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



namespace Mirasvit\Rewards\Ui\Transaction\Listing\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection as GroupCollection;

class CustomerGroup implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;


    public function __construct(GroupCollection $customerGroup)
    {
        $this->customerGroup = $customerGroup;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options = $this->getCustomerGroups();

        return $this->options;
    }

    /**
     * Get customer groups
     *
     * @return array
     */
    public function getCustomerGroups() {
        $customerGroups = $this->customerGroup->toOptionArray();
        return $customerGroups;
    }
}
