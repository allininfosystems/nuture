<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class Sociallogin extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Resource collection
     *
     * @var \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_resourceCollection;

    /**
     * Name of the resource model
     *
     * @var string
     */
    protected $_resourceName;

    /**
     * \Magento\Store\Model\StoreManagerInterface
     *
     * @var string
     */
    protected $_storeManager;
    /**
     * \Magestore\Sociallogin\Helper\Data
     *
     * @var string
     */
    protected $_dataHelper;
    /**
     * \Magento\Customer\Model\CustomerFactory
     *
     * @var string
     */
    protected $_customerFactory;
    /**
     * \Magestore\Sociallogin\Model\AuthorloginFactory
     *
     * @var string
     */
    protected $_authorloginFactory;
    /**
     * \Magestore\Sociallogin\Model\Resource\Authorlogin\CollectionFactory
     *
     * @var string
     */
    protected $_authorCollectionFactory;

    protected $_directory;

    protected $_request;

    /**
     * Sociallogin constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Sociallogin\Helper\Data $Datahelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param AuthorloginFactory $AuthorloginFactory
     * @param ResourceModel\Authorlogin\CollectionFactory $authorCollectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Sociallogin\Helper\Data $Datahelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magestore\Sociallogin\Model\AuthorloginFactory $AuthorloginFactory,
        \Magestore\Sociallogin\Model\ResourceModel\Authorlogin\CollectionFactory $authorCollectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_storeManager = $storeManager;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_resource = $resource;
        $this->_resourceCollection = $resourceCollection;
        $this->_dataHelper = $Datahelper;
        $this->_customerFactory = $customerFactory;
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_authorloginFactory = $AuthorloginFactory;
        $this->_authorCollectionFactory = $authorCollectionFactory;
        $this->_request = $request;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _getBaseDir()
    {
        return $this->_directory->getAbsolutePath();
    }



}

