<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ProductFilter\Model\Attribute\Source;

class ActiveFilters extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Filter list
     * @var \Magento\Catalog\Model\Layer\FilterableAttributeListInterface
     */
    protected $_filterList;

    /**
     * Data helper
     * @var Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Options
     * @var array
     */
    protected $_options = [];

    /**
     * Constructor
     * @param \Plumrocket\ProductFilter\Helper\Data $dataHelper
     */
    public function __construct(
        \Plumrocket\ProductFilter\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        $attrs = $this->_dataHelper->getSelectedAttributes();

        $this->_options[] = ['value' => '-', 'label' => '- - -'];

        if (count($attrs)) {
            $this->_options[] = ['value' => [], 'label' => __('Attributes')];
            foreach ($attrs as $code => $item) {
                // $label = !is_array($item) ? $item : $code . ' (Group)';

                if (is_array($item)) {
                    foreach ($item as $_code => $_label) {
                        $this->_options[] = [
                            'label' => $_label . ' (' . $code . ')',
                            'value' => $_code
                        ];
                    }
                } else {
                    $this->_options[] = [
                        'label' => $item,
                        'value' => $code
                    ];
                }

            }
        }

        $customOptions = $this->_dataHelper->getSelectedCustomOptions();
        if ($customOptions) {
            $this->_options[] = ['value' => [], 'label' => 'Customizable Options'];
            foreach ($customOptions as $option) {
                $this->_options[] = [
                    'label' => $option,
                    'value' => $this->_dataHelper->getCustomOptionRequestVar() . $option
                ];
            }
        }

        return $this->_options;
    }
}
