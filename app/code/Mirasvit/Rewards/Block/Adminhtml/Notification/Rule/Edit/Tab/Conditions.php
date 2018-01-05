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



namespace Mirasvit\Rewards\Block\Adminhtml\Notification\Rule\Edit\Tab;

class Conditions extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions_serialized';

    public function __construct(
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->widgetFormRendererFieldset = $widgetFormRendererFieldset;
        $this->conditions = $conditions;
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
        $notificationRule = $this->registry->registry('current_notification_rule');
        $form = $this->formFactory->create();
;
        $formName = 'rewards_notification_rule_form';
        $fieldsetName = 'rule_actions_fieldset';

        $form->setHtmlIdPrefix($formName);

        $url = $this->getUrl(
            'sales_rule/promo_quote/newConditionHtml/form/'.$formName.$fieldsetName,
            ['form_namespace' => $formName]
        );
        $renderer = $this->widgetFormRendererFieldset
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($url)
            ->setFieldSetId($formName.$fieldsetName);

        $fieldset = $form->addFieldset($fieldsetName, [
            'legend' => __('Apply the rule only if the following conditions are met (leave blank for all products)'),
        ])->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', [
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
            'data-form-part' => $formName,
        ])->setRule($notificationRule)->setRenderer($this->conditions);

        $form->setValues($notificationRule->getData());
        $this->setConditionFormName($notificationRule->getConditions(), $formName);
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
