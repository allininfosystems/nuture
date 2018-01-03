<?php

namespace Cminds\StockNotification\Controller\Index;

use Cminds\StockNotification\Model\ResourceModel\StockNotification\CollectionFactory
    as StockNotificationCollectionFactory;
use Cminds\StockNotification\Model\StockNotificationFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\EmailAddress;

class Signup extends Action
{
    private $stockNotificationFactory;
    private $stockNotificationCollectionFactory;
    private $customerSession;

    /**
     * Signup constructor.
     *
     * @param Context                            $context
     * @param StockNotificationFactory           $stockNotificationFactory
     * @param Session                            $customerSession
     * @param StockNotificationCollectionFactory $stockNotificationCollectionFactory
     */
    public function __construct(
        Context $context,
        StockNotificationFactory $stockNotificationFactory,
        StockNotificationCollectionFactory $stockNotificationCollectionFactory,
        Session $customerSession
    ) {
        parent::__construct($context);

        $this->stockNotificationFactory = $stockNotificationFactory;
        $this->stockNotificationCollectionFactory = $stockNotificationCollectionFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Zend_Validate_Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        if ($this->isAlreadySubscribed() === false) {
            $this->saveStockNotification();
            $this->messageManager->addSuccessMessage(
                __(
                    'We will send an email to %1 when the product will back in stock!',
                    $this->getPostEmail()
                )
            );
        } else {
            $this->messageManager->addNoticeMessage(
                __(
                    'Your e-mail %1 is already on the list!',
                    $this->getPostEmail()
                )
            );
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }

    /**
     * @return string|null
     * @throws \Zend_Validate_Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getPostEmail()
    {
        $email = $this->getRequest()->getPost('email');
        $this->validateEmailFormat($email);

        return $email;
    }

    /**
     * @return string|null
     */
    private function getPostProductId()
    {
        return $this->getRequest()->getParam('product_id');
    }

    /**
     * @return int|null
     */
    private function getCurrentCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     * Save StockNotification to the DB.
     *
     * @return Signup
     * @throws \Exception
     */
    private function saveStockNotification()
    {
        $notification = $this->stockNotificationFactory->create();
        $notification
            ->setData('product_id', $this->getPostProductId())
            ->setData('customer_id', $this->getCurrentCustomerId())
            ->setData('email', $this->getPostEmail())
            ->setData('notified', false)
            ->save();

        return $this;
    }

    /**
     * Check is current email is on the list with specific product id.
     *
     * @return bool
     */
    private function isAlreadySubscribed()
    {
        $collection = $this->stockNotificationCollectionFactory->create()
            ->addFieldToFilter('product_id', $this->getPostProductId())
            ->addFieldToFilter('email', $this->getPostEmail())
            ->addFieldToFilter('notified', false)
            ->getItems();

        return empty($collection) ? false : true;
    }

    /**
     * Validate email.
     *
     * @param string $email
     *
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function validateEmailFormat($email)
    {
        if (!\Zend_Validate::is($email, EmailAddress::class)) {
            throw new LocalizedException(__('Please enter a valid email address.'));
        }
    }
}
