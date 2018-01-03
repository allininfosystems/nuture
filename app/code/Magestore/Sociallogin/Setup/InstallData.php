<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    protected $_eavSetupFactory;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    )
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $installer->startSetup();

        /*your code here*/

        $installer->endSetup();
    }
}
