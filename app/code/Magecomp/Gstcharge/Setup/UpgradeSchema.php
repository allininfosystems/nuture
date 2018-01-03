<?php


namespace Magecomp\Gstcharge\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		 $installer = $setup;
        //$setup->startSetup();
         $installer->startSetup();
      	
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
           
			
			$eavTable = $installer->getTable('directory_country_region');
		
			$columns = [
				'state_code' => [
					'type' => Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'state_code',
					'length'    => '3',
				],
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}	
			$eavTable = $installer->getTable('directory_country_region_name');
		
			$columns = [
				'state_code' => [
					'type' => Table::TYPE_TEXT,
					'nullable' => false,
					'comment' => 'state_code',
					'length'    => '3',
				],
			];
		
			$connection = $installer->getConnection();
			foreach ($columns as $name => $definition) {
				$connection->addColumn($eavTable, $name, $definition);
			}	
			
			
			// Update State code
	
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '35' WHERE `default_name` = 'Andaman and Nicobar Islands';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '35' WHERE `name` = 'Andaman and Nicobar Islands';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '37' WHERE `default_name` = 'Andhra Pradesh';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '37' WHERE `name` = 'Andhra Pradesh';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '12' WHERE `default_name` = 
'Arunachal Pradesh';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '12' WHERE `name` = 'Arunachal Pradesh';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '18' WHERE `default_name` = 'Assam';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '18' WHERE `name` = 'Assam';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '10' WHERE `default_name` = 'Bihar';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '10' WHERE `name` = 'Bihar';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '04' WHERE `default_name` = 'Chandigarh';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '04' WHERE `name` = 'Chandigarh';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '22' WHERE `default_name` = 'Chhattisgarh';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '22' WHERE `name` = 'Chhattisgarh';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '26' WHERE `default_name` = 'Dadra and Nagar Haveli';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '26' WHERE `name` = 'Dadra and Nagar Haveli';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '25' WHERE `default_name` = 'Daman and Diu';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '25' WHERE `name` = 'Daman and Diu';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '07' WHERE `default_name` = 'Delhi';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '07' WHERE `name` = 'Delhi';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '30' WHERE `default_name` = 'Goa';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '30' WHERE `name` = 'Goa';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '24' WHERE `default_name` = 'Gujarat';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '24' WHERE `name` = 'Gujarat';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '06' WHERE `default_name` = 'Haryana';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '06' WHERE `name` = 'Haryana';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '02' WHERE `default_name` = 'Himachal Pradesh';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '02' WHERE `name` = 'Himachal Pradesh';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '01' WHERE `default_name` = 'Jammu and Kashmir';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '01' WHERE `name` = 'Jammu and Kashmir';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '20' WHERE `default_name` = 'Jharkhand';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '20' WHERE `name` = 'Jharkhand';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '29' WHERE `default_name` = 'Karnataka';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '29' WHERE `name` = 'Karnataka';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '32' WHERE `default_name` = 'Kerala';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '32' WHERE `name` = 'Kerala';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '31' WHERE `default_name` = 'Lakshadweep';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '31' WHERE `name` = 'Lakshadweep';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '23' WHERE `default_name` = 'Madhya Pradesh';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '23' WHERE `name` = 'Madhya Pradesh';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '27' WHERE `default_name` = 'Maharashtra';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '27' WHERE `name` = 'Maharashtra';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '14' WHERE `default_name` = 'Manipur';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '14' WHERE `name` = 'Manipur';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '17' WHERE `default_name` = 'Meghalaya';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '17' WHERE `name` = 'Meghalaya';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '15' WHERE `default_name` = 'Mizoram';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '15' WHERE `name` = 'Mizoram';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '13' WHERE `default_name` = 'Nagaland';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '13' WHERE `name` = 'Nagaland';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '21' WHERE `default_name` = 'Orissa';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '21' WHERE `name` = 'Orissa';"); 


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '34' WHERE `default_name` = 'Pondicherry';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '34' WHERE `name` = 'Pondicherry';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '03' WHERE `default_name` = 'Punjab';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '03' WHERE `name` = 'Punjab';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '08' WHERE `default_name` = 'Rajasthan';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '08' WHERE `name` = 'Rajasthan';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '11' WHERE `default_name` = 'Sikkim';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '11' WHERE `name` = 'Sikkim';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '33' WHERE `default_name` = 'Tamil Nadu';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '33' WHERE `name` = 'Tamil Nadu';");

$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '36' WHERE `default_name` = 'Telangana';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '36' WHERE `name` = 'Telangana';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '16' WHERE `default_name` = 'Tripura';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '16' WHERE `name` = 'Tripura';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '09' WHERE `default_name` = 'Uttar Pradesh';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '09' WHERE `name` = 'Uttar Pradesh';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '05' WHERE `default_name` = 'Uttarakhand';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '05' WHERE `name` = 'Uttarakhand';");


$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region')}` SET `state_code` = '19' WHERE `default_name` = 'West Bengal';");
$setup->getConnection()->query("UPDATE `{$installer->getTable('directory_country_region_name')}` SET `state_code` = '19' WHERE `name` = 'West Bengal';");	 
        }


       $installer->endSetup();

    }
}