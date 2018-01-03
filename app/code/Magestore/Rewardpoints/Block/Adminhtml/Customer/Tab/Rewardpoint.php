<?php

namespace Magestore\Rewardpoints\Block\Adminhtml\Customer\Tab;

use Magento\Customer\Controller\RegistryConstants;


class Rewardpoint extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    protected $_rewardAccount;

    /**
     * Credit constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magestore\Rewardpoints\Helper\Customer $helperRewardCustomer
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Rewardpoints\Model\Customer $rewardCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magestore\Rewardpoints\Helper\Point $helperRewardPoint,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Rewardpoints\Model\Customer $rewardAccount,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->_currencyFactory = $currencyFactory;
        $this->_helperRewardPoint = $helperRewardPoint;
        $this->_objectManager = $objectManager;
        $this->_rewardAccount = $rewardAccount;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * get Current Reward Account Model
     *
     * @return \Magestore\Rewardpoints\Model\Customer
     */
    public function getRewardAccount()
    {
        if (!$this->_rewardAccount->getId()) {
            $customerId = $this->getRequest()->getParam('id');
            $this->_rewardAccount->load($customerId, 'customer_id');
        }
        return $this->_rewardAccount;
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rewardpoints_');

        $fieldset = $form->addFieldset('rewardpoints_form', array('legend' =>__('Reward Points Information')));

        $fieldset->addField('point_balance', 'note', array(
            'label' => __('Available Points Balance'),
            'title' => __('Available Points Balance'),
            'text' => '<strong>' . $this->_helperRewardPoint->format(
                    $this->getRewardAccount()->getPointBalance()) . '</strong>',
        ));

        $fieldset->addField('holding_balance', 'note', array(
            'label' => __('On Hold Points Balance'),
            'title' => __('On Hold Points Balance'),
            'text' => '<strong>' . $this->_helperRewardPoint->format(
                    $this->getRewardAccount()->getHoldingBalance()) . '</strong>',
        ));
        $fieldset->addField('spent_balance', 'note', array(
            'label' => __('Spent Points'),
            'title' => __('Spent Points'),
            'text' => '<strong>' . $this->_helperRewardPoint->format(
                    $this->getRewardAccount()->getSpentBalance()) . '</strong>',
        ));

        $fieldset->addField('reward_change_balance', 'text', array(
            'label' => __('Change Balance'),
            'title' => __('Change Balance'),
            'name' => 'rewardpoints[change_balance]',
            'data-form-part' => $this->getData('target_form'),
            'note' => __('Add or subtract customer\'s balance. For ex: 99 or -99 points.'),
        ));

        $fieldset->addField('change_title', 'textarea', array(
            'label' => __('Change Title'),
            'title' => __('Change Title'),
            'name'  => 'rewardpoints[change_title]',
            'data-form-part' => $this->getData('target_form'),
            'style' => 'height: 5em;'
        ));

        $fieldset->addField('expiration_day', 'text', array(
            'label' => __('Points Expire On'),
            'title' => __('Points Expire On'),
            'name'  => 'rewardpoints[expiration_day]',
            'data-form-part' => $this->getData('target_form'),
            'note'  => __('day(s) since the transaction date. If empty or zero, there is no limitation.')
        ));

        $fieldset->addField('admin_editing', 'hidden', array(
            'name'  => 'rewardpoints[admin_editing]',
            'data-form-part' => $this->getData('target_form'),
            'value' => 1,
        ));

        $fieldset->addField('is_notification', 'checkbox', array(
            'label' => __('Update Points Subscription'),
            'title' => __('Update Points Subscription'),
            'name'  => 'rewardpoints[is_notification]',
            'data-form-part' => $this->getData('target_form'),
            'checked'   => $this->getRewardAccount()->getIsNotification(),
            'value' => 1,
            'onclick' => 'this.value = this.checked ? 1 : 0;'
        ));

        $fieldset->addField('expire_notification', 'checkbox', array(
            'label' => __('Expire Transaction Subscription'),
            'title' => __('Expire Transaction Subscription'),
            'name'  => 'rewardpoints[expire_notification]',
            'data-form-part' => $this->getData('target_form'),
            'checked'   => $this->getRewardAccount()->getExpireNotification(),
            'value' => 1,
            'onclick' => 'this.value = this.checked ? 1 : 0;'
        ));

        $form->addFieldset('balance_history_fieldset', array(
        'legend' =>__('Balance History')))->setRenderer($this->_layout
            ->getBlockSingleton('Magento\Backend\Block\Widget\Form\Renderer\Fieldset')
            ->setTemplate('Magestore_Rewardpoints::customer/balancehistory.phtml'));


        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Reward Points');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Reward Points');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        if ($this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)) {
            return true;
        }
        return false;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        if ($this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)) {
            return false;
        }
        return true;
    }


    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}
