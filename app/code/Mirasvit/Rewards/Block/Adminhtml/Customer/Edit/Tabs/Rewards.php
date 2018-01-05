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



namespace Mirasvit\Rewards\Block\Adminhtml\Customer\Edit\Tabs;

use Magento\Customer\Controller\RegistryConstants;

//class Rewards extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
class Rewards extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    protected $rewardsBalance;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

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
     * @var \Magento\Customer\Model\Customer
     */
    private $customerRepository;

    /**
     * @param \Mirasvit\Rewards\Helper\Balance          $rewardsBalance
     * @param \Mirasvit\Rewards\Helper\Data             $rewardsData
     * @param \Magento\Framework\Data\FormFactory       $formFactory
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Backend\Block\Widget\Context     $context
     * @param \Magento\Customer\Model\Customer          $customerRepository
     * @param array                                     $data
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Customer\Model\Customer $customerRepository,
        array $data = []
    ) {
        $this->rewardsBalance     = $rewardsBalance;
        $this->rewardsData        = $rewardsData;
        $this->formFactory        = $formFactory;
        $this->registry           = $registry;
        $this->context            = $context;
        $this->customerRepository = $customerRepository;
        $this->authorization      = $context->getAuthorization();

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getAfter()
    {
        return 'wishlist';
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Reward Points');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Reward Points');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return $this->getId() ? true : false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $form = $this->formFactory->create();
        $form->setHtmlIdPrefix('_rewards');
        $customer = $this->_getCustomer();
        $amount = $this->rewardsBalance->getBalancePoints($customer);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Rewards Information')]);

        $fieldset->addField('balance', 'label',
            [
                'label' => __('Available Points Balance'),
                'name' => 'balance',
                'value' => $this->rewardsData->formatPoints($amount),
            ]
        );

        if ($this->isAllowed()) {
            $fieldset->addField('rewards_change_balance', 'text',
                [
                    'label' => __('Change Balance'),
                    'name' => 'rewards_change_balance',
                    'note' => __('Enter positive or negative number of points. E.g. 10 or -10'),
                    'data-form-part' => 'customer_form',
                ]
            );

            $fieldset->addField('rewards_message', 'text',
                [
                    'label' => __('Message in the rewards history'),
                    'name' => 'rewards_message',
                    'note' => __('Customer will see this in his account'),
                    'data-form-part' => 'customer_form',
                    //                'value' => __('Changed by store administrator')
                ]
            );
        }

        $grid = $this->getLayout()
            ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Customer\Edit\Tabs\Transaction\Grid', 'rewards.grid');

        $html = "
<div class=\"entry-edit\">
{$form->toHtml()}
</div>
<div class=\"entry-edit\">
<div class=\"entry-edit-head\">
    <h4 class=\"icon-head head-edit-form fieldset-legend\">Transactions</h4>
</div>
<div class=\"fieldset \">
    <div class=\"hor-scroll\">
        {$grid->toHtml()}
    </div>
</div>
</div>
        ";

        return $html;
    }

    /**
     * Tab should be loaded trough Ajax call.
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @return \Magento\Customer\Model\Customer|bool
     */
    protected function _getCustomer()
    {
        if ($customerId = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)) {
            $customerData = $this->customerRepository->load($customerId);

            return $customerData;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->authorization->isAllowed('Mirasvit_Rewards::reward_points_transaction');
    }
}
