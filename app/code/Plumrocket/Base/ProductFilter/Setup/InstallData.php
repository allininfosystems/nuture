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

namespace Plumrocket\ProductFilter\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * Resource config
     * @var Magento\Config\Model\ResourceModel\Config
     */
    private $_resourceConfig;

    /**
     * Filterable attributes
     * @var Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    private $_filterableAttributes;

    /**
     * Json helper
     * @var Magento\Framework\Json\Helper\Data
     */
    private $_jsonHelper;

    /**
     * Constructor
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $filterableAttributes
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $filterableAttributes,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_resourceConfig = $resourceConfig;
        $this->_jsonHelper = $jsonHelper;
        $this->_filterableAttributes = $filterableAttributes;
    }

    /**
     * Install Data
     * @param  ModuleDataSetupInterface $setup
     * @param  ModuleContextInterface   $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId   = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Display Settings');

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'pr_exluded_attributes',
            [
                'type' => 'text',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Disable Filter Attributes and Customizable Options',
                'input' => 'multiselect',
                'class' => '',
                'source' => 'Plumrocket\ProductFilter\Model\Attribute\Source\ActiveFilters',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => 0,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'position' => 260
            ]
        );

        $eavSetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $attributeGroupId,
            'pr_exluded_attributes',
            '260'
        );

        $collection = $this->_filterableAttributes->create();
        $collection->setItemObjectClass('Magento\Catalog\Model\ResourceModel\Eav\Attribute')
            ->setOrder('position', 'ASC');

        $collection->addIsFilterableFilter();

        $attributeCodes =[];
        foreach ($collection as $key => $attribute) {
            if ($attribute->getFrontendLabel()) {
                $attributeCodes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }

        $value = $this->_jsonHelper->jsonEncode($attributeCodes);
        $this->_resourceConfig->saveConfig(
            'prproductfilter/' . \Plumrocket\ProductFilter\Helper\Data::FILTER_ENABLED_ATTRIBUTES,
            $value,
            'default',
            0
        );
    }
}
