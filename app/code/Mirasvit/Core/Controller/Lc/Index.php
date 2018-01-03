<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-core
 * @version   1.2.21
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Controller\Lc;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Module\ModuleList;

class Index extends Action
{
    /**
     * @var ModuleList
     */
    protected $moduleList;

    /**
     * @param ModuleList $moduleList
     * @param Context $context
     */
    public function __construct(
        ModuleList $moduleList,
        Context $context
    ) {
        $this->moduleList = $moduleList;

        parent::__construct($context);
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        echo '<pre>';
        foreach ($this->moduleList->getNames() as $name) {
            if (substr($name, 0, 9) == 'Mirasvit_') {
                echo substr($name, 9).PHP_EOL;
            }
        }

        exit;
    }
}
