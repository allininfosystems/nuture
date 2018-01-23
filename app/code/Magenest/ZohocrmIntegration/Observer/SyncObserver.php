<?php
/**
 * Created by PhpStorm.
 * User: canhnd
 * Date: 15/02/2017
 * Time: 08:58
 */
namespace Magenest\ZohocrmIntegration\Observer;

use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

abstract class SyncObserver implements ObserverInterface
{
    protected $pathEnable = '';
    protected $pathSyncOption = '';

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    protected $scopeConfig;

    public function __construct(
        QueueFactory $queueFactory,
        ScopeConfigInterface $config
    ) {
        $this->queueFactory = $queueFactory;
        $this->scopeConfig = $config;
    }

    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    public function addToQueue($type, $entityId)
    {
        /** add to queue mode */
        $queue = $this->queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('entity_id', $entityId)
            ->getFirstItem();
        if ($queue->getId()) {
            /** Creditmemo existed in queue */
            $queue =  $this->queueFactory->create()->load($queue->getId());
            $queue->setEnqueueTime(time());
            $queue->save();
            return;
        }
        $queue = $this->queueFactory->create();
        $data = [
            'type' =>  $type,
            'entity_id' => $entityId,
            'enqueue_time' => time(),
            'priority' => 1,
        ];
        $queue->setData($data);
        $queue->save();
        return;
    }
}
