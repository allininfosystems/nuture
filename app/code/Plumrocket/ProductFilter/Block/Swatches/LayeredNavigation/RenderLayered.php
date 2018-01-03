<?php

namespace Plumrocket\ProductFilter\Block\Swatches\LayeredNavigation;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Framework\View\Element\Template;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;


class RenderLayered extends \Magento\Swatches\Block\LayeredNavigation\RenderLayered
{
    /**
     * Data helper
     * @var Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Url helper
     * @var Plumrocket\ProductFilter\Helper\Url
     */
    protected $_urlHelper;

    protected $_currentOption;

    /**
     * @param Template\Context $context
     * @param Attribute $eavAttribute
     * @param AttributeFactory $layerAttribute
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Swatches\Helper\Media $mediaHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Plumrocket\ProductFilter\Helper\Url $urlHelper,
        array $data = []
    ) {

        $this->_dataHelper = $dataHelper;
        $this->_urlHelper = $urlHelper;

        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttribute,
            $swatchHelper,
            $mediaHelper,
            $data
        );
    }


    /**
     * {@inheritdoc}
     */
    public function buildUrl($attributeCode, $value)
    {

        if ($this->_dataHelper->moduleEnabled()) {

            if ($this->_urlHelper->useSeoFriendlyUrl()) {

                if ($currentFilter = $this->_request->getParam($attributeCode)) {
                    $_currentFilterOptions = explode(',', $currentFilter);

                    if (in_array($this->_currentOption->getValue(), $_currentFilterOptions)) {
                        return $this->_urlHelper->getResetUrl(
                            $attributeCode,
                            $this->_dataHelper->getConvertedAttributeValue($value)
                        );
                    }
                }

                $url = $this->_urlHelper->getUrlForItem(
                    $attributeCode,
                    $this->_dataHelper->getConvertedAttributeValue($value)
                );

                $url = $this->_urlHelper->checkUrl($url);
                return $url;
            } else {
                $values = explode(',', $this->_request->getParam($attributeCode));
                foreach($values as $k => $v) {
                    if (!$v) {
                        unset($values[$k]);
                    }
                }
                $key = array_search($value, $values);
                if ($key !== false) {
                    unset($values[$key]);
                } else {
                    $values[] = $value;
                }

                if (!count($values)) {
                    $value = null;
                } else {
                    sort($values);
                    $value = implode(',', $values);
                }

            }
        }

        return parent::buildUrl($attributeCode, $value);
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptionViewData(FilterItem $filterItem, Option $swatchOption)
    {
        $this->_currentOption = $swatchOption;
        if ($this->_dataHelper->moduleEnabled() && $this->_urlHelper->useSeoFriendlyUrl()) {
            $style = '';
            $link = $this->buildUrl($this->eavAttribute->getAttributeCode(), $filterItem->getLabel());
            if ($this->isOptionDisabled($filterItem)) {
                $link = 'javascript:void();';
                $style = 'disabled';
            }

            return [
                'link' => $link,
                'custom_style' => $style,
                'label' => $swatchOption->getLabel()
            ];
        }

        return parent::getOptionViewData($filterItem, $swatchOption);
    }
}
