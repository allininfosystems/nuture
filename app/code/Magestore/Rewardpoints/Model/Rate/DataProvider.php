<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Model\Rate;

use Magento\Eav\Model\Config;
use Magento\Ui\DataProvider\EavValidationRules;
use Magestore\Rewardpoints\Model\ResourceModel\Rate\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterPool;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var string
     */
    protected $fieldsetName;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CustomerCollectionFactory $customerCollectionFactory,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $customerCollectionFactory->create();
        if(isset($data['fieldset_name']) && $data['fieldset_name']) {
            $this->fieldsetName = $data['fieldset_name'];
        }
        $meta['money']['addbefore'] = $localeCurrency->getCurrency($directoryHelper->getBaseCurrencyCode())->getSymbol();
        $meta['sort_order']['notice'] = __('Higher priority Rate will be applied first');
        $this->meta[$this->fieldsetName]['fields'] = $meta;
    }
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $rate) {
            $result[$this->fieldsetName] = $rate->getData();
            $this->loadedData[$rate->getId()] = $result;
        }
        return $this->loadedData;
    }
}
