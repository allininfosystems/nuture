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



namespace Mirasvit\Rewards\Model\Earning;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @method \Mirasvit\Rewards\Model\ResourceModel\Earning\Behavior\Collection getCollection()
 * @method \Mirasvit\Rewards\Model\Earning\Behavior load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Rewards\Model\Earning\Behavior setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Rewards\Model\Earning\Behavior setIsMassStatus(bool $flag)
 * @method \Mirasvit\Rewards\Model\ResourceModel\Earning\Behavior getResource()
 */
class Behavior extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'rewards_earning_behavior';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewards_earning_behavior';
    /**
     * @var string
     */
    protected $_eventPrefix = 'rewards_earning_behavior';

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Rewards\Model\ResourceModel\Earning\Behavior');
    }

    /**
     * @param bool|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /************************/

    /**
     * @param string $code
     * @return bool|Behavior
     */
    public function getByActionCode($code)
    {
        $instance = $this->getCollection()
            ->addFieldToFilter('action_code', $code)
            ->addFieldToFilter('is_active', 1)
            ->getFirstItem();

        if ($instance->getId()) {
            return $instance;
        }

        return false;
    }
}
