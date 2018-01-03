<?php
namespace Magestore\Rewardpoints\Model;


class Rate  extends \Magento\Framework\Model\AbstractModel
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**
     * Rate direction - Spending Point
     */
    const POINT_TO_MONEY = 1;

    /**
     * Rate direction - Earning Point
     */
    const MONEY_TO_POINT = 2;

    /**
     * Redefine event Prefix, event object
     *
     * @var string
     */
    protected $_eventPrefix = 'rewardpoints_rate';
    protected $_eventObject = 'rewardpoints_rate';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Rate constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Rewardpoints\Model\ResourceModel\Rate');
    }

    public function getRate($direction = 1, $customerGroupId = null, $websiteId = null)
    {

        if (is_null($customerGroupId)) {
            $customerGroupId = $this->_customerSession->getCustomerGroupId();

        }
        if (is_null($websiteId)) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        }

        $rateCollection = $this->getCollection()
            ->addFieldToFilter('direction', $direction)
            ->addFieldToFilter('website_ids', array('finset' => $websiteId))
            ->addFieldToFilter('customer_group_ids', array('finset' => $customerGroupId))
            ->addFieldToFilter('points', array('gt' => 0))
            ->addFieldToFilter('status', array('eq' => self::STATUS_ACTIVE))
            ->addFieldToFilter('money', array('gt' => 0));
        $rateCollection->getSelect()->order('sort_order DESC');
        $rateCollection->getSelect()->order('rate_id DESC');
        $rate = $rateCollection->getFirstItem();

        if ($rate && $rate->getId()) {
            return $rate;
        }
        return false;
    }



}