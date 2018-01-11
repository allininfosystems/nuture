<?php

namespace Nuluv\Pincode\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0){

		$installer->run('create table pincodelist(id int not null auto_increment, pincode varchar(10), message varchar(255), status varchar(4), primary key(id))');


		//demo 
//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//$scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
//demo 

		}

        $installer->endSetup();

    }
}