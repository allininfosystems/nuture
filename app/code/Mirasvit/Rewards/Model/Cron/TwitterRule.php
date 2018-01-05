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



namespace Mirasvit\Rewards\Model\Cron;

use Abraham\TwitterOAuth\TwitterOAuth;
use Mirasvit\Rewards\Model\ResourceModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TwitterRule
{

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Queue\CollectionFactory
     */
    protected $earningRuleQueueCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    /**
     * @param \Magento\Customer\Model\CustomerFactory            $customerFactory
     * @param ResourceModel\Earning\Rule\Queue\CollectionFactory $earningRuleQueueCollectionFactory
     * @param \Mirasvit\Rewards\Model\Config                     $config
     * @param \Mirasvit\Rewards\Helper\Behavior                  $rewardsBehavior
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        ResourceModel\Earning\Rule\Queue\CollectionFactory $earningRuleQueueCollectionFactory,
        \Mirasvit\Rewards\Model\Config $config,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior
    ) {
        $this->customerFactory = $customerFactory;
        $this->earningRuleQueueCollectionFactory = $earningRuleQueueCollectionFactory;
        $this->config = $config;
        $this->rewardsBehavior = $rewardsBehavior;
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->earnTwitterPoints();
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function earnTwitterPoints()
    {
        $connection = new TwitterOAuth(
            $this->getConfig()->getTwitterConsumerKey(),
            $this->getConfig()->getTwitterConsumerSecret()
        );

        $connection->setDecodeJsonAsArray(true);

        $data = $connection->oauth2("oauth2/token", ["grant_type" => 'client_credentials']);

        if (isset($data['token_type']) && $data['token_type'] == 'bearer') {
            $this->getConfig()->setTwitterToken($data['access_token']);
            $this->getConfig()->setTwitterIsTokenActive(true);
        }

        $connection->setDecodeJsonAsArray(false);

        $collection = $this->earningRuleQueueCollectionFactory->create()
            ->addFieldToFilter('is_processed', 0);

        /** @var \Mirasvit\Rewards\Model\Earning\Rule\Queue $item */
        foreach ($collection as $item) {
            $search = str_replace($item->getRuleType().'-', '', $item->getRuleCode());
            $customer = $this->customerFactory->create()->load($item->getCustomerId());
            $date = explode(' ', $item->getCreatedAt());
            $since = $date[0];
            $connection = new TwitterOAuth(
                $this->getConfig()->getTwitterConsumerKey(),
                $this->getConfig()->getTwitterConsumerSecret(),
                null,
                $this->getConfig()->getTwitterToken()
            );
            $data = $connection->get(
                "search/tweets", [
                    'q' => $search,
                    'since' => $since,
                ]
            );
            if (!empty($data->statuses)) {
                if (count($data->statuses)) {
                    $transaction = $this->rewardsBehavior->processRule(
                        $item->getRuleType(),
                        $customer,
                        $item->getWebsiteId(),
                        $search,
                        []
                    );

                    if ($transaction) {
                        $item->setIsProcessed(1)->save();
                    }
                }
            }
        }
    }
}
