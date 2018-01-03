<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Model\Transaction;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type;
use Magestore\Rewardpoints\Model\Rate;
use Magento\Ui\DataProvider\EavValidationRules;
use Magestore\Rewardpoints\Model\ResourceModel\Rate\Collection;
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
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var FilterPool
     */
    protected $filterPool;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * EAV attribute properties to fetch from meta storage
     * @var array
     */
    protected $metaProperties = [
        'dataType' => 'frontend_input',
        'visible' => 'is_visible',
        'required' => 'is_required',
        'label' => 'frontend_label',
        'sortOrder' => 'sort_order',
        'notice' => 'note',
        'default' => 'default_value',
        'afterElement' => 'after_element_html',
        'size' => 'multiline_count',
    ];

    /**
     * Form element mapping
     *
     * @var array
     */
    protected $formElement = [
        'text' => 'input',
        'hidden' => 'input',
        'boolean' => 'checkbox',
    ];

    /**
     * @var EavValidationRules
     */
    protected $eavValidationRules;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param EavValidationRules $eavValidationRules
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param Config $eavConfig
     * @param FilterPool $filterPool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        EavValidationRules $eavValidationRules,
        CustomerCollectionFactory $customerCollectionFactory,
        Config $eavConfig,
        FilterPool $filterPool,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->eavValidationRules = $eavValidationRules;
        $this->collection = $customerCollectionFactory->create();
        $this->eavConfig = $eavConfig;
        $this->filterPool = $filterPool;
       
    }
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $rate) {
            $result['rewardpoints_earningrates'] = $rate->getData();
            $this->loadedData[$rate->getId()] = $result;
        }
        return $this->loadedData;
    }
}
