<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block\Adminhtml\System;

use Magento\Framework\App\Filesystem\DirectoryList;

class Container extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_url;
    /**
     * helper data.
     *
     * @var \Magestore\Sociallogin\Helper\Data
     */
    protected $_dataHelper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     * @var \Magento\Framework\UrlInterface $url
     * @var \Magestore\Sociallogin\Helper\Data $Datahelper
     * @var \Magento\Framework\Session\SessionManagerInterface $session
     * @var \Magento\Framework\Filesystem $filesystem
     */
    protected $_session;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\Sociallogin\Helper\Data $Datahelper,
        \Magento\Framework\Session\SessionManagerInterface $session,
        array $data = []
    )
    {
        $this->_storeManager = $context->getStoreManager();
        $this->_url = $context->getUrlBuilder();
        $this->_directory = $context->getFilesystem()->getDirectoryWrite(DirectoryList::ROOT);
        $this->_dataHelper = $Datahelper;
        $this->_session = $session;
        parent::__construct($context, $data);

    }

    protected function _getBaseDir()
    {
        return $this->_directory->getAbsolutePath();
    }

    public function _getStore()
    {

        $storeId = (int)$this->getRequest()->getParam('store', 0);

        return $this->_storeManager->getStore($storeId);
    }

}
