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


/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mirasvit\Rewards\Console\Command;

use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mirasvit\Rewards\Model\ResourceModel;
use Magento\Framework\ObjectManagerInterface;

class EarnTwitterPointsCommand extends Command
{
    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $helper;

    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Earning\Rule\Queue\CollectionFactory
     */
    protected $earningRuleQueueCollectionFactory;

    /**
     * @param ObjectManagerInterface                             $objectManager
     * @param \Mirasvit\Rewards\Helper\Behavior                  $helper
     * @param \Magento\Customer\Model\CustomerFactory            $customerFactory
     * @param \Mirasvit\Rewards\Model\Config                     $config
     * @param ResourceModel\Earning\Rule\Queue\CollectionFactory $earningRuleQueueCollectionFactory
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        \Mirasvit\Rewards\Helper\Behavior $helper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Mirasvit\Rewards\Model\Config $config,
        ResourceModel\Earning\Rule\Queue\CollectionFactory $earningRuleQueueCollectionFactory
    ) {
        $this->objectManager = $objectManager;
        $this->helper = $helper;
        $this->customerFactory = $customerFactory;
        $this->config = $config;
        $this->earningRuleQueueCollectionFactory = $earningRuleQueueCollectionFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:rewards:twitter-reward-points')
            ->setDescription('Rewards Twitter points');
        parent::configure();
    }

    /**
     * @return \Mirasvit\Rewards\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start:<info>');
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

        $processed = 0;
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
                    $transaction = $this->helper->processRule(
                        $item->getRuleType(),
                        $customer,
                        $item->getWebsiteId(),
                        $search,
                        []
                    );
                    if ($transaction) {
                        $item->setIsProcessed(1)->save();
                        $processed++;
                    }
                }
            }
        }
        $output->writeln('<info>processed: ' . $processed . ' tweet(s)<info>');
        $output->writeln('<info>end<info>');
    }
}