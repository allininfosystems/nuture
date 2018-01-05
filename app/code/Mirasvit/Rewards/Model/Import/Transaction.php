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


namespace Mirasvit\Rewards\Model\Import;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Transaction extends \Magento\ImportExport\Model\Import\AbstractEntity
{
    /**#@+
     * Error codes
     */
    const ERROR_WEBSITE_IS_EMPTY = 'websiteIsEmpty';
    const ERROR_EMAIL_IS_EMPTY = 'emailIsEmpty';
    const ERROR_INVALID_WEBSITE = 'invalidWebsite';
    const ERROR_INVALID_EMAIL = 'invalidEmail';
    const ERROR_VALUE_IS_REQUIRED = 'valueIsRequired';
    const ERROR_CUSTOMER_NOT_FOUND = 'customerNotFound';

    /**
     * @var string
     */
    private $transactionColumn = 'transaction_id';

    /**
     * @var string
     */
    private $websiteColumn = 'website_id';

    /**
     * @var string
     */
    private $customerColumn = 'customer_email';

    /**
     * Transaction DB table name.
     *
     * @var string
     */
    protected $entityTable;

    /**
     * Customer model
     *
     * @var \Mirasvit\Rewards\Model\Transaction
     */
    protected $transactionModel;

    /**
     * {@inheritdoc}
     */
    protected $masterAttributeCode = 'customer_email';

    /**
     * Website code-to-ID
     *
     * @var array
     */
    protected $websiteCodeToId = [];

    /**
     * All stores code-ID pairs.
     *
     * @var array
     */
    protected $storeCodeToId = [];

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->storeManager    = $storeManager;
        $this->storageFactory  = $storageFactory;
        $this->customerFactory = $customerFactory;

        parent::__construct(
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $resource,
            $errorAggregator,
            $data
        );

        $this->addMessageTemplate(self::ERROR_WEBSITE_IS_EMPTY, __('Please specify a website.'));
        $this->addMessageTemplate(self::ERROR_EMAIL_IS_EMPTY, __('Please specify a customer email.'));
        $this->addMessageTemplate(self::ERROR_INVALID_WEBSITE, __('We found an invalid value in a website column.'));
        $this->addMessageTemplate(self::ERROR_INVALID_EMAIL, __('Please enter a valid email.'));
        $this->addMessageTemplate(self::ERROR_VALUE_IS_REQUIRED, __('Please make sure attribute "%s" is not empty.'));
        $this->addMessageTemplate(
            self::ERROR_CUSTOMER_NOT_FOUND,
            __('We can\'t find a customer who matches this email and website code.')
        );

        $this->initStores(true);

        $this->transactionModel = $transactionFactory->create();

        $this->entityTable  = $this->transactionModel->getResource()->getMainTable();

        $this->initWebsites(true);
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'rewards_transaction';
    }

    /**
     * Retrieve website id by code or false when website code not exists
     *
     * @param string $websiteCode
     * @return int|false
     */
    public function getWebsiteId($websiteCode)
    {
        if (isset($this->websiteCodeToId[$websiteCode])) {
            return $this->websiteCodeToId[$websiteCode];
        }

        return false;
    }

    /**
     * Initialize website values
     *
     * @param bool $withDefault
     * @return $this
     */
    protected function initWebsites($withDefault = false)
    {
        /** @var $website \Magento\Store\Model\Website */
        foreach ($this->storeManager->getWebsites($withDefault) as $website) {
            $this->websiteCodeToId[$website->getCode()] = $website->getId();
        }
        return $this;
    }

    /**
     * Initialize stores data
     *
     * @param bool $withDefault
     * @return $this
     */
    protected function initStores($withDefault = false)
    {
        /** @var $store \Magento\Store\Model\Store */
        foreach ($this->storeManager->getStores($withDefault) as $store) {
            $this->storeCodeToId[$store->getCode()] = $store->getId();
        }
        return $this;
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int   $rowNumber
     * @return bool
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        if (isset($this->_validatedRows[$rowNumber])) {
            // check that row is already validated
            return !$this->getErrorAggregator()->isRowInvalid($rowNumber);
        }
        $this->_validatedRows[$rowNumber] = true;
        $this->_processedEntitiesCount++;
        if ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE) {
            $this->checkRowForUpdate($rowData, $rowNumber);
        } elseif ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE) {
            $this->checkRowForDelete($rowData, $rowNumber);
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNumber);
    }

    /**
     * Import data rows
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _importData()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entitiesToCreate = [];
            $entitiesToUpdate = [];
            $entitiesToDelete = [];

            foreach ($bunch as $rowNumber => $rowData) {
                if (!$this->validateRow($rowData, $rowNumber)) {
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNumber);
                    continue;
                }
                if ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE) {
                    $entitiesToDelete[] = $rowData[$this->masterAttributeCode];
                } elseif ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE) {
                    /** @var \Magento\Customer\Model\Customer $customer */
                    $customer = $this->customerFactory->create()->getCollection()
                        ->addFieldToFilter('email', $rowData[$this->customerColumn])->getFirstItem();
                    $rowData['customer_id'] = $customer->getId();

                    $processingData = $this->prepareDataForUpdate($rowData, $rowNumber);

                    $existTransactions = $this->transactionModel->getCollection()->getAllIds();
                    if (
                        !empty($processingData[$this->transactionColumn]) &&
                        in_array($processingData[$this->transactionColumn], $existTransactions)
                    ) {
                        $entitiesToUpdate[] = $processingData;
                    } else {
                        if (array_key_exists($this->transactionColumn, $processingData)) {
                            unset($processingData[$this->transactionColumn]);
                        }

                        $entitiesToCreate[] = $processingData;
                    }
                }
            }
            $this->updateItemsCounterStats($entitiesToCreate, $entitiesToUpdate, $entitiesToDelete);
            /**
             * Save prepared data
             */
            if ($entitiesToCreate || $entitiesToUpdate) {
                $this->saveTransactionEntities($entitiesToCreate, $entitiesToUpdate);
            }
            if ($entitiesToDelete) {
                $this->deleteTransactionEntities($entitiesToDelete);
            }
        }

        return true;
    }

    /**
     * Prepare customer data for update
     *
     * @param array $rowData
     * @param int   $transactionNumber
     * @return array
     */
    protected function prepareDataForUpdate(array $rowData, $transactionNumber)
    {
        if (empty($rowData['created_at'])) {
            $rowData['created_at'] = (new \DateTime())
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        } else {
            $rowData['created_at'] = (new \DateTime())->setTimestamp(strtotime($rowData['created_at']))
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }
        if (!empty($rowData['expires_at'])) {
            $rowData['expires_at'] = (new \DateTime())->setTimestamp(strtotime($rowData['expires_at']))
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }

        if (!array_key_exists('updated_at', $rowData)) {
            $rowData['updated_at'] = $rowData['created_at'];
        }
        if (!array_key_exists('code', $rowData) || empty($rowData['code'])) {
            $rowData['code'] = (string)$rowData['code'];
        }
        $transactionNumber = '#' . $transactionNumber;
        if (!empty($rowData[$this->transactionColumn])) {
            $transactionNumber = $rowData[$this->transactionColumn];
        }
        $rowData['code'] = 'import of transaction ' . $transactionNumber . ' - ' . $rowData['code'];

        $columns = $this->_connection->describeTable($this->entityTable);
        foreach (array_keys($rowData) as $key) {
            if (!isset($columns[$key])) {
                unset($rowData[$key]);
            }
        }

        return $rowData;
    }

    /**
     * Update and insert data in entity table
     *
     * @param array $entitiesToCreate Rows for insert
     * @param array $entitiesToUpdate Rows for update
     * @return $this
     */
    protected function saveTransactionEntities(array $entitiesToCreate, array $entitiesToUpdate)
    {
        if ($entitiesToCreate) {
            $this->_connection->insertMultiple($this->entityTable, $entitiesToCreate);
        }

        if ($entitiesToUpdate) {
            $this->_connection->insertOnDuplicate(
                $this->entityTable,
                $entitiesToUpdate
            );
        }

        return $this;
    }

    /**
     * @param array $entitiesToDelete
     * @return $this
     */
    protected function deleteTransactionEntities(array $entitiesToDelete)
    {
        $condition = $this->_connection->quoteInto('transaction_id IN (?)', $entitiesToDelete);
        $this->_connection->delete($this->entityTable, $condition);

        return $this;
    }

    /**
     * @param array $rowData
     * @param int   $rowNumber
     * @return bool
     */
    protected function checkRowForDelete(array $rowData, $rowNumber)
    {
        if (empty($rowData[$this->masterAttributeCode])) {
            $this->addRowError(static::ERROR_VALUE_IS_REQUIRED, $rowNumber, $this->masterAttributeCode);
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNumber);
    }
    /**
     * @param array $rowData
     * @param int   $rowNumber
     *
     * @return void
     */
    protected function checkRowForUpdate(array $rowData, $rowNumber)
    {
        if (empty($rowData[$this->customerColumn])) {
            $this->addRowError(static::ERROR_EMAIL_IS_EMPTY, $rowNumber, $this->customerColumn);
        } else {
            $email = strtolower($rowData[$this->customerColumn]);

            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                $this->addRowError(static::ERROR_INVALID_EMAIL, $rowNumber, $this->customerColumn);
            }
            if (
                isset($rowData[$this->websiteColumn]) &&
                !isset($this->websiteCodeToId[$rowData[$this->websiteColumn]])
            ) {
                $this->addRowError(static::ERROR_INVALID_WEBSITE, $rowNumber, $this->websiteColumn);
            }
            $customer = $this->customerFactory->create()->getCollection()
                ->addFieldToFilter('email', $email)->getFirstItem();
            if (!$customer || !$customer->getId()) {
                $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
            }
        }
    }
}
