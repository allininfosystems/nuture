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



namespace Mirasvit\Rewards\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @method \Mirasvit\Rewards\Model\ResourceModel\ReferralLink\Collection getCollection()
 * @method \Mirasvit\Rewards\Model\ReferralLink load(int $id)
 * @method \Mirasvit\Rewards\Model\ResourceModel\ReferralLink getResource()
 * @method int getCustomerId()
 * @method \Mirasvit\Rewards\Model\ReferralLink setCustomerId(int $entityId)
 * @method string getReferralLink()
 * @method \Mirasvit\Rewards\Model\ReferralLink setReferralLink(string $link)
 */
class ReferralLink extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'rewards_customer_referral_link';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewards_customer_referral_link';
    /**
     * @var string
     */
    protected $_eventPrefix = 'rewards_customer_referral_link';

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
     * @var \Magento\Framework\Math\Random
     */
    protected $random;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @param \Magento\Framework\Math\Random                          $random
     * @param \Magento\Customer\Model\Session                         $session
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Math\Random $random,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->random = $random;
        $this->session = $session;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Rewards\Model\ResourceModel\ReferralLink');
    }

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function createReferralLinkId($customerId)
    {
        $unique = false;
        while (!$unique) {
            $link = $this->random->getRandomString(8);
            $collection = $this->getCollection()->addFieldToFilter('referral_link', $link);
            if (!$collection->count()) {
                $unique = true;
            }
        }

        $this->unsetData();
        $this->setCustomerId($customerId);
        $this->setReferralLink($link);
        $this->save();

        return $this;
    }
}
