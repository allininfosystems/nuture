<?php

namespace Magestore\Rewardpoints\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    public function __construct()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $this->insertPage();
        /**
         * your code here
         */

        $installer->endSetup();
    }

    public function insertPage(){


        $policyPage = $this->_objectManager->create('Magento\Cms\Model\Page')->checkIdentifier('rewardpoints-policy', 0);
        if($policyPage){
            $this->_objectManager->create('Magento\Cms\Model\Page')->load($policyPage)->delete();
        }
        $welcomePage = $this->_objectManager->create('Magento\Cms\Model\Page')->checkIdentifier('rewardpoints-welcome', 0);
        if($welcomePage){
            $this->_objectManager->create('Magento\Cms\Model\Page')->load($welcomePage)->delete();
        }

        $policycmsPageData = array(
            'title' => 'Reward Policy',
            'page_layout' => '2columns-left',
            'meta_keywords' => 'reward policy',
            'meta_description' => 'reward policy',
            'identifier' => 'rewardpoints-policy',
            'content_heading' => 'Reward Policy',
            'stores' => array(0), //available for all store views
            'is_active' => '1',
            'content' => '<div>
            <style>
                strong.rewardpoints-title {
                    text-transform: uppercase;
                }
            </style>
            <div class="page-title-wrapper">
                <h1 class="page-title">
                    <span class="base" data-ui-id="page-title-wrapper">Reward Policy</span>    
                </h1>
            </div>
    {{block class="Magestore\Rewardpoints\Block\RewardpointTemplate" template="rewardpoints/policy/earn.phtml" }}
    <div class="rewardpoints-dashboard-list">
        <strong class="rewardpoints-title">{{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} EXCHANGE RATES</strong>
        <br>
        <p class="rewardpoints-title-content">The value of {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} is determined by an exchange rate of both currency spent on products to {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}}, and an exchange rate of {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} earned to currency for spending on future purchases.</p>
    </div>
    <div class="rewardpoints-dashboard-list">
        <strong class="rewardpoints-title">REDEEM {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}}</strong>
        <br>
        <p class="rewardpoints-title-content">You can redeem your {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} at checkout. If you have accumulated enough {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} to redeem them you will have the option of using {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} as one of the payment methods. The option to use {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}}, as well as your balance and the monetary equivalent this balance, will be shown to you in the Payment Method area of the checkout. Redeemable {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} can be used in conjunction with other payment methods such as credit cards, gift cards and more.</p>
    </div>
    <div class="rewardpoints-dashboard-list">
        <strong class="rewardpoints-title">{{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} MINIMUMS AND MAXIMUMS</strong>
        <p class="rewardpoints-title-content">{{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} may be capped at a minimum value required for redemption. If this option is selected you will not be able to use your {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} until you accrue a minimum number of {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}}, at which {{config path="rewardpoints/general/point_name"}} they will become available for redemption.
</br>{{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} may also be capped at the maximum value of {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} which can be accrued. If this option is selected you will need to redeem your accrued {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} before you are able to earn more {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}}.
</p>
    </div>
    <div class="rewardpoints-dashboard-list">
        <strong class="rewardpoints-title">MANAGE YOUR {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}}</strong>
        <p class="rewardpoints-title-content">You have the ability to view and manage your {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} through your <a href="{{block class="Magestore\Rewardpoints\Block\RewardpointTemplate" template="rewardpoints/policy/linkCustomer.phtml" }}">Customer Account</a>. From your account you will be able to view your total {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} (and currency equivalent), minimum needed to redeem, whether you have reached the maximum {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} limit and a cumulative history of {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} acquired, redeemed and lost. The history record will retain and display historical rates and currency for informational purposes. The history will also show you comprehensive informational messages regarding {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}}, including expiration notifications.
</p>
    </div>
    <div class="rewardpoints-dashboard-list">
        <strong class="rewardpoints-title">{{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} EXPIRATION</strong>
        <p class="rewardpoints-title-content">{{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} can be set to expire. Points will expire in the order form which they were first earned.</p>
        <strong>Note:</strong>
        <ul class="rewardpoints-dashboard-ul">
            <li>Points can be used as store credit in our system only. Redeeming to cash is not allowed.</li>
            <li>You can sign up to receive email notifications each time your balance changes when you either earn, redeem or lose {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}}, as well as point expiration notifications. This option is found in the {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} section of the My Account area.</li>
        </ul>
    </div>
</div>

',
        );
        $this->_objectManager->create('Magento\Cms\Model\Page')->setData($policycmsPageData)->save();

        $welcomecmsPageData = array(
            'title' => 'Reward Welcome Page',
            'page_layout' => '2columns-left',
            'meta_keywords' => 'reward welcome page',
            'meta_description' => 'reward welcome page',
            'identifier' => 'rewardpoints-welcome',
            'content_heading' => 'WELCOME TO OUR REWARD PROGRAM!',
            'stores' => array(0), //available for all store views
            'is_active' => '1',
            'content' => '<div>
             <style>
                strong.rewardpoints-title {
                    text-transform: uppercase;
                }
            </style>
    <div class="rewardpoints-dashboard-list">
        Every of your activity on our site is appreciated & rewarded. The more you spend, the more you save. Enroll now to begin earning greater benefits! 
        If you are already a member, log in to view your Reward Balance.
    </div>
    <div class="rewardpoints-dashboard-list">
        <strong class="rewardpoints-title rewardpoints-title-upercase">BENEFITS OF {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} FOR REGISTERED CUSTOMERS</strong>
        <p class="rewardpoints-title-content"><img src="{{media url="rewardpoints/default/welcome.png"}}" alt="{{block class="Magestore\Rewardpoints\Block\Welcome\Name"}} Welcome" /></p>

        <p class="rewardpoints-title-content">
            Once you register you will be able to earn and accrue {{block class="Magestore\Rewardpoints\Block\Welcome\Name"}}, which are then redeemable at time of purchase towards the cost of your order. Rewards are an added bonus to your shopping experience on the site and just one of the ways we thank you for being a loyal customer. You can easily earn points for certain actions you take on the site, such as making purchases.
        </p>
    </div>
    <div class="rewardpoints-dashboard-list">
        {{block class="Magestore\Rewardpoints\Block\Account\Dashboard\Earn" template="rewardpoints/account/dashboard/earn.phtml"}}
    </div>
    <div class="rewardpoints-dashboard-list">
        {{block class="Magestore\Rewardpoints\Block\Account\Dashboard\Spend" template="rewardpoints/account/dashboard/spend.phtml"}}
    </div>
</div>'
        );
        $this->_objectManager->create('Magento\Cms\Model\Page')->setData($welcomecmsPageData)->save();
    }
}
