<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Sociallogin extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_FBLOGIN_ACTIVE = 'fblogin/is_active';
    const XML_PATH_GOLOGIN_ACTIVE = 'gologin/is_active';
    const XML_PATH_TWLOGIN_ACTIVE = 'twlogin/is_active';
    const XML_PATH_YALOGIN_ACTIVE = 'yalogin/is_active';
    const XML_PATH_SOCIAL_ENABLE = 'general/enable_socials';
    const XML_PATH_SOCIAL_DIRECTION = 'general/direction';
    const XML_PATH_SOCIAL_NUMBERSHOW = 'general/number_show';
    const XML_PATH_OPENLOGIN_ACTIVE = 'openlogin/is_active';
    const XML_PATH_LJLOGIN_ACTIVE = 'ljlogin/is_active';
    const XML_PATH_LINKLOGIN_ACTIVE = 'linklogin/is_active';
    const XML_PATH_AOLLOGIN_ACTIVE = 'aollogin/is_active';
    const XML_PATH_WPLOGIN_ACTIVE = 'wplogin/is_active';
    const XML_PATH_CALLOGIN_ACTIVE = 'callogin/is_active';
    const XML_PATH_ORGLOGIN_ACTIVE = 'orglogin/is_active';
    const XML_PATH_FQLOGIN_ACTIVE = 'fqlogin/is_active';
    const XML_PATH_LIVELOGIN_ACTIVE = 'livelogin/is_active';
    const XML_PATH_PERLOGIN_ACTIVE = 'perlogin/is_active';
    const XML_PATH_SEOLOGIN_ACTIVE = 'selogin/is_active';
    const XML_PATH_VKLOGIN_ACTIVE = 'vklogin/is_active';
    const XML_PATH_INSTALOGIN_ACTIVE = 'instalogin/is_active';
    const XML_PATH_AMAZONLOGIN_ACTIVE = 'amazonlogin/is_active';

    const XML_PATH_FBLOGIN_ORDER = 'fblogin/sort_order';
    const XML_PATH_GOLOGIN_ORDER = 'gologin/sort_order';
    const XML_PATH_TWLOGIN_ORDER = 'twlogin/sort_order';
    const XML_PATH_YALOGIN_ORDER = 'yalogin/sort_order';
    const XML_PATH_OPENLOGIN_ORDER = 'openlogin/sort_order';
    const XML_PATH_LJLOGIN_ORDER = 'ljlogin/sort_order';
    const XML_PATH_LINKLOGIN_ORDER = 'linklogin/sort_order';
    const XML_PATH_AOLLOGIN_ORDER = 'aollogin/sort_order';
    const XML_PATH_WPLOGIN_ORDER = 'wplogin/sort_order';
    const XML_PATH_CALLOGIN_ORDER = 'callogin/sort_order';
    const XML_PATH_ORGLOGIN_ORDER = 'orglogin/sort_order';
    const XML_PATH_FQLOGIN_ORDER = 'fqlogin/sort_order';
    const XML_PATH_LIVELOGIN_ORDER = 'livelogin/sort_order';
    const XML_PATH_PERLOGIN_ORDER = 'perlogin/sort_order';
    const XML_PATH_SEOLOGIN_ORDER = 'selogin/sort_order';
    const XML_PATH_VKLOGIN_ORDER = 'vklogin/sort_order';
    const XML_PATH_INSTALOGIN_ORDER = 'instalogin/sort_order';
    const XML_PATH_AMAZONLOGIN_ORDER = 'amazonlogin/sort_order';

    /**
     * @var \Magestore\Sociallogin\Helper\Data
     */
    protected $_dataHelper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlinterface;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;

    public function __construct(

        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Sociallogin\Helper\Data $dataHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    )
    {
        $this->_dataHelper = $dataHelper;
        $this->_urlinterface = $context->getUrlBuilder();
        $this->_objectManager = $objectManager;
        $this->_storeManager = $context->getStoreManager();
        $this->_customerSession = $customerSession;
        $this->_session = $context->getSession();
        parent::__construct($context, $data);
    }

    public function _getSession()
    {
        return $this->_customerSession;
    }

    public function _getSingtoneSession()
    {
        return $this->_session;
    }

    public function getCurrentUrl()
    {
        return $this->_urlinterface->getCurrentUrl();
    }

    public function isShowFaceBookButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_FBLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowGmailButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_GOLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowTwitterButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_TWLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowYahooButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_YALOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function getDirection()
    {
        return $this->_dataHelper->getConfig(self::XML_PATH_SOCIAL_DIRECTION, $this->_storeManager->getStore()->getId());
    }

    public function getIsActive()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_SOCIAL_ENABLE, $this->_storeManager->getStore()->getId());
    }

    public function isShowLinkedButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_LINKLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowAolButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_AOLLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowWpButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_WPLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowCalButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_CALLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowOrgButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_ORGLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowFqButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_FQLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowLiveButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_LIVELOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowOpenButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_OPENLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowLjButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_LJLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowPerButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_PERLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function isShowSeButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_SEOLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function getFacebookButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Fblogin')
            ->setTemplate('Magestore_Sociallogin::bt_fblogin.phtml')->toHtml();

    }

    public function getGmailButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Gologin')
            ->setTemplate('Magestore_Sociallogin::bt_gologin.phtml')->toHtml();

    }

    public function getTwitterButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Twlogin')
            ->setTemplate('Magestore_Sociallogin::bt_twlogin.phtml')->toHtml();

    }

    public function getYahooButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Yalogin')
            ->setTemplate('Magestore_Sociallogin::bt_yalogin.phtml')->toHtml();
    }

    public function getOpenButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Openlogin')
            ->setTemplate('Magestore_Sociallogin::bt_openlogin.phtml')->toHtml();
    }

    public function getLjButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Ljlogin')
            ->setTemplate('Magestore_Sociallogin::bt_ljlogin.phtml')->toHtml();
    }

    public function getLinkedButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Linkedlogin')
            ->setTemplate('Magestore_Sociallogin::bt_linkedlogin.phtml')->toHtml();
    }

    public function getAolButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Aollogin')
            ->setTemplate('Magestore_Sociallogin::bt_aollogin.phtml')->toHtml();
    }

    public function getWpButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Wplogin')
            ->setTemplate('Magestore_Sociallogin::bt_wplogin.phtml')->toHtml();
    }

    public function getAuWp()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Wplogin')
            ->setTemplate('Magestore_Sociallogin::au_wp.phtml')->toHtml();
    }

    public function getCalButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Callogin')
            ->setTemplate('Magestore_Sociallogin::bt_callogin.phtml')->toHtml();
    }

    public function getAuCal()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Calllogin')
            ->setTemplate('Magestore_Sociallogin::au_cal.phtml')->toHtml();
    }

    public function getOrgButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Orglogin')
            ->setTemplate('Magestore_Sociallogin::bt_orglogin.phtml')->toHtml();
    }

    public function getFqButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Fqlogin')
            ->setTemplate('Magestore_Sociallogin::bt_fqlogin.phtml')->toHtml();
    }

    public function getLiveButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Livelogin')
            ->setTemplate('Magestore_Sociallogin::bt_livelogin.phtml')->toHtml();
    }

    public function getPerButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Perlogin')
            ->setTemplate('Magestore_Sociallogin::bt_perlogin.phtml')->toHtml();
    }

    public function getSeButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Selogin')
            ->setTemplate('Magestore_Sociallogin::bt_selogin.phtml')->toHtml();
    }

    public function getVkButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Vklogin')
            ->setTemplate('Magestore_Sociallogin::bt_vklogin.phtml')->toHtml();
    }

    public function getInsButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Inslogin')
            ->setTemplate('Magestore_Sociallogin::bt_inslogin.phtml')->toHtml();

    }

    public function getAmazonButton()
    {
        return $this->getLayout()->createBlock('Magestore\Sociallogin\Block\Amazon')
            ->setTemplate('Magestore_Sociallogin::bt_amazonlogin.phtml')->toHtml();

    }

    public function sortOrderFaceBook()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_FBLOGIN_ORDER);
    }

    public function sortOrderGmail()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_GOLOGIN_ORDER);
    }

    public function sortOrderTwitter()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_TWLOGIN_ORDER);
    }

    public function sortOrderYahoo()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_YALOGIN_ORDER);
    }

    public function sortOrderOpen()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_OPENLOGIN_ORDER);
    }

    public function sortOrderLj()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_LJLOGIN_ORDER);
    }

    public function sortOrderLinked()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_LINKLOGIN_ORDER);
    }

    public function sortOrderAol()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_AOLLOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderWp()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_WPLOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderCal()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_CALLOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderOrg()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_ORGLOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderFq()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_FQLOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderLive()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_LIVELOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderPer()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_PERLOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderSe()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_SEOLOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function isShowVkButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_VKLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderVk()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_VKLOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function isShowInsButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_INSTALOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderIns()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_INSTALOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function isShowAmazonButton()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_AMAZONLOGIN_ACTIVE, $this->_storeManager->getStore()->getId());
    }

    public function sortOrderAmazon()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_AMAZONLOGIN_ORDER, $this->_storeManager->getStore()->getId());
    }

    public function getNumberShow()
    {
        return (int)$this->_dataHelper->getConfig(self::XML_PATH_SOCIAL_NUMBERSHOW, $this->_storeManager->getStore()->getId());
    }

    protected function _beforeToHtml()
    {
        if (!$this->getIsActive()) {
            $this->setTemplate(null);
        }

        if ($this->_getSession()->isLoggedIn()) {
            $this->setTemplate(null);
        }

        return parent::_beforeToHtml();
    }

    public function makeArrayButton()
    {
        $buttonArray = array();
        if ($this->isShowAmazonButton()) {
            $buttonArray[] = [
                'button' => $this->getAmazonButton(),
                'check' => $this->isShowAmazonButton(),
                'id' => 'bt-loginamazon',
                'sort' => $this->sortOrderAmazon(),
            ];
        }

        if ($this->isShowInsButton()) {
            $buttonArray[] = [
                'button' => $this->getInsButton(),
                'check' => $this->isShowInsButton(),
                'id' => 'bt-loginins',
                'sort' => $this->sortOrderIns(),
            ];
        }

        if ($this->isShowFaceBookButton()) {
            $buttonArray[] = [
                'button' => $this->getFacebookButton(),
                'check' => $this->isShowFaceBookButton(),
                'id' => 'bt-loginfb',
                'sort' => $this->sortOrderFaceBook(),
            ];
        }

        if ($this->isShowGmailButton()) {
            $buttonArray[] = [
                'button' => $this->getGmailButton(),
                'check' => $this->isShowGmailButton(),
                'id' => 'bt-logingo',
                'sort' => $this->sortOrderGmail(),
            ];
        }

        if ($this->isShowTwitterButton()) {
            $buttonArray[] = [
                'button' => $this->getTwitterButton(),
                'check' => $this->isShowTwitterButton(),
                'id' => 'bt-logintw',
                'sort' => $this->sortOrderTwitter(),
            ];
        }

        if ($this->isShowYahooButton()) {
            $buttonArray[] = [
                'button' => $this->getYahooButton(),
                'check' => $this->isShowYahooButton(),
                'id' => 'bt-loginya',
                'sort' => $this->sortOrderYahoo(),
            ];
        }

        if ($this->isShowAolButton()) {
            $buttonArray[] = [
                'button' => $this->getAolButton(),
                'check' => $this->isShowAolButton(),
                'id' => 'bt-loginaol',
                'sort' => $this->sortOrderAol(),
            ];
        }

        if ($this->isShowWpButton()) {
            $buttonArray[] = [
                'button' => $this->getWpButton(),
                'check' => $this->isShowWpButton(),
                'id' => 'bt-loginwp',
                'sort' => $this->sortOrderWp(),
            ];
        }

        if ($this->isShowCalButton()) {
            $buttonArray[] = [
                'button' => $this->getCalButton(),
                'check' => $this->isShowCalButton(),
                'id' => 'bt-logincal',
                'sort' => $this->sortOrderCal(),
            ];
        }

        if ($this->isShowOrgButton()) {
            $buttonArray[] = [
                'button' => $this->getOrgButton(),
                'check' => $this->isShowOrgButton(),
                'id' => 'bt-loginorg',
                'sort' => $this->sortOrderOrg(),
            ];
        }

        if ($this->isShowFqButton()) {
            $buttonArray[] = [
                'button' => $this->getFqButton(),
                'check' => $this->isShowFqButton(),
                'id' => 'bt-loginfq',
                'sort' => $this->sortOrderFq(),
            ];
        }

        if ($this->isShowLiveButton()) {
            $buttonArray[] = [
                'button' => $this->getLiveButton(),
                'check' => $this->isShowLiveButton(),
                'id' => 'bt-loginlive',
                'sort' => $this->sortOrderLive(),
            ];
        }

        if ($this->isShowLinkedButton()) {
            $buttonArray[] = [
                'button' => $this->getLinkedButton(),
                'check' => $this->isShowLinkedButton(),
                'id' => 'bt-loginlinked',
                'sort' => $this->sortOrderLinked(),
            ];
        }

        if ($this->isShowOpenButton()) {
            $buttonArray[] = [
                'button' => $this->getOpenButton(),
                'check' => $this->isShowOpenButton(),
                'id' => 'bt-loginopen',
                'sort' => $this->sortOrderOpen(),
            ];
        }

        if ($this->isShowLjButton()) {
            $buttonArray[] = [
                'button' => $this->getLjButton(),
                'check' => $this->isShowLjButton(),
                'id' => 'bt-loginlj',
                'sort' => $this->sortOrderLj(),
            ];
        }

        if ($this->isShowPerButton()) {
            $buttonArray[] = [
                'button' => $this->getPerButton(),
                'check' => $this->isShowPerButton(),
                'id' => 'bt-loginper',
                'sort' => $this->sortOrderPer(),
            ];
        }

        if ($this->isShowSeButton()) {
            $buttonArray[] = [
                'button' => $this->getSeButton(),
                'check' => $this->isShowSeButton(),
                'id' => 'bt-loginse',
                'sort' => $this->sortOrderSe(),
            ];
        }

        if ($this->isShowVkButton()) {
            $buttonArray[] = [
                'button' => $this->getVkButton(),
                'check' => $this->isShowVkButton(),
                'id' => 'bt-loginvk',
                'sort' => $this->sortOrderVk(),
            ];
        }

        usort($buttonArray, [$this, 'compareSortOrder']);
        return $buttonArray;
    }

    public function compareSortOrder($a, $b)
    {
        if ($a['sort'] == $b['sort']) {
            return 0;
        }

        return $a['sort'] < $b['sort'] ? -1 : 1;
    }

}