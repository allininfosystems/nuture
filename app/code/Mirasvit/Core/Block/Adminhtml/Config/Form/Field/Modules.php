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


namespace Mirasvit\Core\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Model\ModuleFactory;

class Modules extends Field
{
    /**
     * @var ModuleFactory
     */
    protected $moduleFactory;

    public function __construct(
        ModuleFactory $moduleFactory,
        Context $context,
        array $data = []
    ) {
        $this->moduleFactory = $moduleFactory;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('config/form/field/modules.phtml');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return \Mirasvit\Core\Model\Module[]
     */
    public function getModules()
    {
        $modules = [];

        foreach ($this->moduleFactory->create()->getInstalledModules() as $moduleName) {
            $module = $this->moduleFactory->create()
                    ->load($moduleName);

            if ($module->getName()) {
                $modules[] = $module;
            }
        }

        return $modules;
    }
}
