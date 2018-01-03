<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Ui\Component\MassAction\Transaction\Status;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;

/**
 * Class Options
 */
class Options implements JsonSerializable
{
    protected $_modelTransaction;

    protected $_optionsStatus;
    /**
     * @var array
     */
    protected $options;

    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    protected $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    protected $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        array $data = [],
        \Magestore\Rewardpoints\Model\Transaction $transaction
    ) {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
        $this->_modelTransaction = $transaction;

    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $options = [
                [
                    'label' => __('Complete'),
                    'value' => $this->_modelTransaction->getConst('STATUS_COMPLETED')
                ],
                [
                    'label' => __('Canceled'),
                    'value' => $this->_modelTransaction->getConst('STATUS_CANCELED')
                ],
                [
                    'label' => __('Expired'),
                    'value' => $this->_modelTransaction->getConst('STATUS_EXPIRED')
                ],

            ];
            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->options[$optionCode['value']] = [
                    'type' => 'status_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$optionCode['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $optionCode['value']]
                    );
                }

                $this->options[$optionCode['value']] = array_merge_recursive(
                    $this->options[$optionCode['value']],
                    $this->additionalData
                );
            }
            $this->options = array_values($this->options);
        }
        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    protected function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
