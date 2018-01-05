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
use Magento\Framework\Profiler;

/**
 * @method \Mirasvit\Rewards\Model\ResourceModel\Purchase\Collection|\Mirasvit\Rewards\Model\Purchase[] getCollection()
 * @method \Mirasvit\Rewards\Model\Purchase load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Rewards\Model\Purchase setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Rewards\Model\Purchase setIsMassStatus(bool $flag)
 * @method \Mirasvit\Rewards\Model\ResourceModel\Purchase getResource()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Purchase extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'rewards_purchase';
    /**
     * @var string
     */
    protected $_cacheTag = 'rewards_purchase';
    /**
     * @var string
     */
    protected $_eventPrefix = 'rewards_purchase';

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
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Spend
     */
    protected $rewardsBalanceSpend;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    protected $rewardsBalance;

    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Earn
     */
    protected $rewardsBalanceEarn;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

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
     * @var \Mirasvit\Rewards\Helper\Rule\Notification
     */
    protected $rewardsPurchase;

    /**
     * @param \Magento\Quote\Model\QuoteFactory                       $quoteFactory
     * @param \Magento\Customer\Model\SessionFactory                  $sessionFactory
     * @param \Mirasvit\Rewards\Model\Config                          $config
     * @param \Mirasvit\Rewards\Helper\Balance\Spend                  $rewardsBalanceSpend
     * @param \Mirasvit\Rewards\Helper\Balance                        $rewardsBalance
     * @param \Mirasvit\Rewards\Helper\Balance\Earn                   $rewardsBalanceEarn
     * @param \Mirasvit\Rewards\Helper\Rule\Notification              $rewardsPurchase
     * @param \Magento\Customer\Model\Session                         $customerSession
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Mirasvit\Rewards\Model\Config $config,
        \Mirasvit\Rewards\Helper\Balance\Spend $rewardsBalanceSpend,
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Mirasvit\Rewards\Helper\Balance\Earn $rewardsBalanceEarn,
        \Mirasvit\Rewards\Helper\Rule\Notification $rewardsPurchase,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->sessionFactory = $sessionFactory;
        $this->config = $config;
        $this->rewardsBalanceSpend = $rewardsBalanceSpend;
        $this->rewardsBalance = $rewardsBalance;
        $this->rewardsBalanceEarn = $rewardsBalanceEarn;
        $this->rewardsPurchase = $rewardsPurchase;
        $this->customerSession = $customerSession;
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
        $this->_init('Mirasvit\Rewards\Model\ResourceModel\Purchase');
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
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote = null;

    /**
     * @return bool|\Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if (!$this->getQuoteId()) {
            return false;
        }
        if (!$this->quote) {
            $this->quote = $this->quoteFactory->create()->load($this->getQuoteId());
        }

        return $this->quote;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxPointsNumberToSpent()
    {
        if (!$this->sessionFactory->create()->isLoggedIn()) {
            return 0;
        }

        return $this->getSpendMaxPoints();
    }

    /**
     * @param float $subtotal
     * @return bool|float|int
     */
    protected function getSpendLimit($subtotal)
    {
        $limit = $this->config->getGeneralSpendLimit();
        if (!$limit) {
            return false;
        }
        if (strpos($limit, '%')) {
            $limit = str_replace('%', '', $limit);
            $limit = (int) $limit;
            $spendLimit = $subtotal * $limit / 100;
            if ($spendLimit > 0) {
                return $spendLimit;
            }
        } else {
            return $limit;
        }
    }

    /**
     * @param int $customerId
     * @return int
     */
    public function getCustomerBalancePoints($customerId)
    {
        return $this->rewardsBalance->getBalancePoints($customerId);
    }

    /**
     * Quote can be changed time to time. So we have to refresh avaliable points number.
     *
     * @param bool $forceRefresh - must be used only if we 100% sure that it will be called once.
     *
     * @return object
     */
    public function refreshPointsNumber($forceRefresh = false)
    {
        $quote = $this->getQuote();
        if (!$forceRefresh && !$this->hasChanges($quote)) {
            return $this;
        }

        Profiler::start('rewards:refreshPointsNumber');

        $this->refreshQuote(false); //reset points discount

        //recalculate spending points
        $cartRange = $this->rewardsBalanceSpend->getCartRange($quote);

        $pointsNumber = (int) $this->getSpendPoints();
        if ($pointsNumber != 0 && $pointsNumber < $cartRange->getMinPoints()) {
            $pointsNumber = $cartRange->getMinPoints();
        }
        if ($pointsNumber > $cartRange->getMaxPoints()) {
            $pointsNumber = $cartRange->getMaxPoints();
        }

        $balancePoints = $this->getCustomerBalancePoints($quote->getCustomerId());
        $cartMax = min($cartRange->getMaxPoints(), $balancePoints);
        if ($pointsNumber > $balancePoints) {
            $pointsNumber = $balancePoints;
            if ($pointsNumber < $cartRange->getMinPoints()) {
                $this->setSpendPoints(0);
                $this->setSpendAmount(0);
                $this->setSpendMinPoints($cartRange->getMinPoints());
                $this->setSpendMaxPoints($cartMax);
            }
        }

        $cartPoints = $this->rewardsBalanceSpend->getCartPoints($quote, $pointsNumber);

        $this->setSpendPoints($cartPoints->getPoints());
        $this->setSpendAmount($cartPoints->getAmount());
        $this->setSpendMinPoints($cartRange->getMinPoints());
        $this->setSpendMaxPoints($cartMax);
        $this->save(); //we need this. otherwise points are not updated in the magegiant checkout ajax.

        $this->refreshQuote(true); //apply points discount with rewards discount

        //recalculate earning points
        $earn = $this->rewardsBalanceEarn->getPointsEarned($quote);
        $this->setEarnPoints($earn);
        Profiler::stop('rewards:refreshPointsNumber');

        $this->rewardsPurchase->calcNotificationMessages();

        return $this;
    }

    /**
     * @param bool $withRewardsDiscount
     * @return void
     */
    protected function refreshQuote($withRewardsDiscount)
    {
        $quote = $this->getQuote();
        if (!$quote->getCustomerId()) {
            return;
        }
        Profiler::start('rewards:refreshQuote');
        $this->config->setCalculateTotalFlag($withRewardsDiscount);
        $isPurchaseSave = $quote->getIsPurchaseSave(); // we don't need to recalculate points now
        $quote->setIsPurchaseSave(true);

        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->setTotalsCollectedFlag(false)
            ->collectTotals()
            ->save()
        ;
        $quote->setIsPurchaseSave($isPurchaseSave);
        Profiler::stop('rewards:refreshQuote');
    }

    /**
     * Check quote for changes.
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return bool
     */
    protected function hasChanges($quote)
    {
        $cachedQuote = $this->customerSession->getRWCachedQuote();
        $data = [];
        $data[] = (int) $quote->getCustomerId();//we need this, because if customer do 'reorder', customer id maybe = 0.
        $data[] = (double) $quote->getData('grandtotal');
        $data[] = (double) $quote->getData('subtotal');
        $data[] = (double) $quote->getData('subtotal_with_discount');
        $data[] = count($quote->getAllItems());

        if ($shipping = $quote->getShippingAddress()) {
            $data[] = $shipping->getData('shipping_method');
            $data[] = (double) $shipping->getData('shipping_amount');
        }
        if ($payment = $quote->getPayment()) {
            if ($payment->hasMethodInstance()) {
                $data[] = $payment->getMethodInstance()->getCode();
            }
        }

        $newCachedQuote = implode('|', $data);

        if ($cachedQuote != $newCachedQuote) {
            $this->customerSession->setRWCachedQuote($newCachedQuote);
            return true;
        }

        return false;
    }
}
