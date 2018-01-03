<?php
namespace Magecomp\Gstcharge\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\Customer\Model\Customer;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
	 private $customerSetupFactory;
    public function __construct(EavSetupFactory $eavSetupFactory,
	CustomerSetupFactory $customerSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
		$this->customerSetupFactory = $customerSetupFactory;
    }
	
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        //$setup->startSetup();
  		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		$eavSetup->addAttribute(
            Product::ENTITY,
            'gst_source',
            [
                'group' => 'Indian GST',
        		'label' => 'GST Rate(in Percentage)',
				'type'  => 'varchar',
        		'input' => 'select',
        		'source' => '\Magecomp\Gstcharge\Model\Source\Percentage',
                'required' => false,
                'sort_order' => 1,
                'global' => Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false
            ]
        );
		$eavSetup->addAttribute(
            Product::ENTITY,
            'hsncode',
            [
                'group' => 'Indian GST',
        		'label' => 'HSN Code',
				'type'  => 'varchar',
        		'input' => 'text',
        		'required' => false,
                'sort_order' => 6,
                'searchable' => false,
				'filterable' => false,
            ]
        );
		$eavSetup->addAttribute(
            Product::ENTITY,
            'gst_source_after_minprice',
            [
                'group' => 'Indian GST',
        		'label' => 'GST Rate If Product Price Below Minimum Set Price',
				'type'  => 'varchar',
        		'input' => 'select',
        		'required' => false,
                'sort_order' => 7,
                'searchable' => false,
				'filterable' => false,
				'global' => Attribute::SCOPE_STORE,
				'source' => '\Magecomp\Gstcharge\Model\Source\Percentage',
            ]
        );
		$eavSetup->addAttribute(
            Product::ENTITY,
            'gst_source_minprice',
            [
                'group' => 'Indian GST',
        		'label' => 'Minimum Product Price to Apply GST Rate',
				'type'  => 'decimal',
        		'input' => 'text',
        		'required' => false,
                'sort_order' => 8,
                'searchable' => false,
				'filterable' => false,
				'length'    => '10,2',
				'global' => Attribute::SCOPE_STORE,
            ]
        );
		 //Category Attribute Create Script
		$eavSetup->addAttribute(
            Category::ENTITY,
            'gst_cat_source',
            [
                'group' => 'Indian GST',
        		'label' => 'GST Rate(in Percentage)',
				'type'  => 'varchar',
        		'input' => 'select',
        		'source' => '\Magecomp\Gstcharge\Model\Source\Percentage',
                'required' => false,
                'sort_order' => 90,
                'global' => Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => true
            ]
        );
		
      	$eavSetup->addAttribute(
            Category::ENTITY,
            'gst_cat_source_minprice',
            [
                'group' => 'Indian GST',
        		'label' => 'Minimum Product Price to Apply GST Rate',
				'type'  => 'decimal',
        		'input' => 'text',
        		'required' => false,
                'sort_order' => 91,
                'global' => Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => true
            ]
        );

		$eavSetup->addAttribute(
           Category::ENTITY,
            'gst_cat_source_after_minprice',
            [
                'group' => 'Indian GST',
        		'label' => 'GST Rate If Product Price Below Minimum Set Price',
				'type'  => 'varchar',
        		'input' => 'select',
        		'source' => '\Magecomp\Gstcharge\Model\Source\Percentage',
                'required' => false,
                'sort_order' => 92,
                'global' => Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => true
            ]
        );
		/*Customer Attribute code start*/
		
		/*Customer Attribute code end*/
		$setup->endSetup();
    }
}