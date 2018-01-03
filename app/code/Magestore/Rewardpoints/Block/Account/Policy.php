<?php
namespace Magestore\Rewardpoints\Block\Account;

class Policy extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_modelPage;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * Policy constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Cms\Model\Page $modelPage
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Page $modelPage,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider

    )
    {
        parent::__construct($context, []);
        $this->_modelPage = $modelPage;
        $this->_filterProvider = $filterProvider;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * @return mixed
     */

    public function getPageIdentifier(){
        return $this->_scopeConfig->getValue(
            \Magestore\Rewardpoints\Helper\Policy::XML_PATH_POLICY_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * @return mixed
     */
    public function getPageId(){
        $pageId = $this->_modelPage->checkIdentifier($this->getPageIdentifier(), $this->_storeManager->getStore()->getId());
        return $pageId;
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    public function getPage(){
        return $this->_modelPage->load($this->getPageId());
    }

    /**
     * @return string
     */
    protected function _toHtml(){

        $html = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
        $html .= $this->_filterProvider->getPageFilter()->filter($this->getPage()->getContent());
        return $html;
    }
}
