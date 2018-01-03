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

namespace Plumrocket\ProductFilter\Block\Adminhtml\System\Config\Form;

class AbstractAttribute extends \Magento\Config\Block\System\Config\Form\Field implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{

    /**
     * Template
     * @var string
     */
    protected $_template = 'Plumrocket_ProductFilter::filter/attribute.phtml';

    /**
     * Element id
     * @var string
     */
    protected $_elementId;

    /**
     * Values
     * @var Array
     */
    protected $_values = [];

    /**
     * Active attributes
     * @var Array
     */
    protected $_active = [];

    /**
     * Not active attributes
     * @var Array
     */
    protected $_notActive = [];

    /**
     * Filter List
     * @var Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $_filterableAttributes;

    /**
     * Data helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Custom option factory
     * @var Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * Config id (Id wich settet in system.xml)
     * @var string
     */
    protected $_configId;

    /**
     * Is custom options
     * @var boolean
     */
    protected $_isCustomOptions = false;

    /**
     * Current element
     */
    protected $_element;

    /**
     * Constructor
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $filterableAttributes
     * @param \Magento\Framework\App\ResourceConnection                                $resourceConnection
     * @param \Plumrocket\ProductFilter\Model\Config\Source\AdditionalFilters          $additionalFilters
     * @param \Plumrocket\ProductFilter\Helper\Data                                    $dataHelper
     * @param \Magento\Backend\Block\Template\Context                                  $context
     * @param array                                                                    $data
     */
    public function __construct(
        \Plumrocket\ProductFilter\Model\FilterList $filterableAttributes,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->_filterableAttributes = $filterableAttributes;
        $this->_dataHelper = $dataHelper;

        $this->_resourceConnection = $resourceConnection;
        parent::__construct($context, $data);
    }

     /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_values = $this->_prepareValue();
        $this->_prepareAttributes();
        $this->_element = $element;
        $this->_elementId = $element->getId();

        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);

        // Disable element if value is inherited from other scope. Flag has to be set before the value is rendered.
        if ($element->getInherit() == 1 && $isCheckboxRequired) {
            $this->setIsDisabled(true);
        }

        $config = $element->getFieldConfig();
        $this->_configId = $config['id'];

        $html = $element->getElementHtml() . $this->toHtml();

        return $html;
    }

    /**
     * Retrieve inherit checkobux html
     * @return string
     */
    public function getInheritCheckboxHtml()
    {
        if ($this->_element === null) {
            return false;
        }

        $isCheckboxRequired = $this->_isInheritCheckboxRequired($this->_element);
        if ($isCheckboxRequired) {
            return $this->_renderInheritCheckbox($this->_element);
        }

        return '<td class=""></td>';
    }

    /**
     * Retrieve element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Retrieve config id
     * @return string
     */
    public function getConfigId()
    {
        return $this->_configId;
    }

    /**
     * Retrieve element id
     * @return string
     */
    public function getElementId()
    {
        return $this->_elementId;
    }

    /**
     * Retrieve active attributes
     * @return Array
     */
    public function getActiveAttributes()
    {
        return $this->_active;
    }

    /**
     * Retrieve not active attributes
     * @return Array
     */
    public function getNotActiveAttributes()
    {
        return $this->_notActive;
    }

    /**
     * Prepare selected attributes
     * @param  string $value
     * @return Array
     */
    protected function _prepareValue()
    {
        return [];
    }

    /**
     * Get all attributes
     * @return Array
     */
    protected function _prepareAttributes()
    {
        return $this;
    }

    /**
     * Is custom options
     * @return boolean
     */
    public function isCustomOptions()
    {
        return $this->_isCustomOptions;
    }
}
