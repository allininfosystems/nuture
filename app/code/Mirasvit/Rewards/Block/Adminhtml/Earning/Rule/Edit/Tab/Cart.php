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
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit\Tab;

class Cart extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Rewards\Model\System\Source\Cartearningstyle
     */
    protected $systemSourceCartearningstyle;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $widgetFormRendererFieldset;

    /**
     * @var \Magento\Rule\Block\Actions
     */
    protected $actions;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Model\System\Source\Cartearningstyle $systemSourceCartearningstyle
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset   $widgetFormRendererFieldset
     * @param \Magento\Rule\Block\Actions                            $actions
     * @param \Magento\Framework\Data\FormFactory                    $formFactory
     * @param \Magento\Framework\Registry                            $registry
     * @param \Magento\Backend\Block\Widget\Context                  $context
     * @param array                                                  $data
     */
    public function __construct(
        \Mirasvit\Rewards\Model\System\Source\Cartearningstyle $systemSourceCartearningstyle,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset,
        \Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Actions $actions,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->systemSourceCartearningstyle = $systemSourceCartearningstyle;
        $this->widgetFormRendererFieldset = $widgetFormRendererFieldset;
        $this->actions = $actions;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mirasvit\Rewards\Model\Earning\Rule $earningRule */
        $earningRule = $this->registry->registry('current_earning_rule');
        $form = $this->formFactory->create();
        $type = $earningRule->getType();
        switch ($type) {
            case 'cart':
                $formName = 'rewards_earning_rule_cart_form';
                break;

            case 'product':
                $formName = 'rewards_earning_rule_product_form';
                break;

            case 'behavior':
                $formName = 'rewards_earning_rule_behavior_form';
                break;

            default:
                break;
        }
        $fieldsetName = 'rule_actions_fieldset';

        $form->setHtmlIdPrefix($formName);

        $url = $this->getUrl(
            'rewards/earning_rule/newConditionHtml/form/'.$formName.$fieldsetName,
            ['form_namespace' => $formName]
        );
        $renderer = $this->widgetFormRendererFieldset
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($url)
            ->setFieldSetId($formName.$fieldsetName);

        $fieldset = $form->addFieldset($fieldsetName, [
            'legend' => __(
                'Apply the rule only to cart items matching the following conditions (leave blank for all items)'
            ),
        ])->setRenderer($renderer);

        $fieldset->addField('actions', 'text', [
            'name' => 'actions',
            'label' => __('Apply To'),
            'title' => __('Apply To'),
            'required' => true,
            'data-form-part' => $formName
        ])->setRule($earningRule)->setRenderer($this->actions);

        $form->setValues($earningRule->getData());
        $this->setConditionFormName($earningRule->getConditions(), $formName);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Handles addition of form name to condition and its conditions.
     *
     * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }

    /************************/
}
