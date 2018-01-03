<?php

namespace Cminds\StockNotification\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Cminds\StockNotification\Model\StockNotificationFactory;

class InstallData implements InstallDataInterface
{
    /**
     * StockNotificationFactory object.
     *
     * @var StockNotificationFactory
     */
    private $stockNotificationFactory;

    /**
     * Temp variable for test.
     *
     * @var bool
     */
    private $installSampleDataAllowed = false;

    /**
     * InstallData constructor.
     *
     * @param StockNotificationFactory $stockNotificationFactory
     */
    public function __construct(
        StockNotificationFactory $stockNotificationFactory
    ) {
        $this->stockNotificationFactory = $stockNotificationFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {

        if ($this->installSampleDataAllowed) {
            $this->installSampleData();
        }
    }

    /**
     * Generate sample data.
     *
     * @return array
     */
    protected function generateSampleData()
    {
        $data = [];
        // determine how many rows have to be generated
        $rowsQty = 1000;

        // change day, month, year to get specific range of customer requests
        for ($i = 0; $i <= $rowsQty; $i++) {
            $day = rand(1, 30);
            $month = rand(1, 12);
            $year = 2017;

            $timestamp = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, $day, $year));

            $values =  [
                'product_id' => rand(1, 20),
                'customer_id' => 1,
                'email' => 'test' . $i * 3 . '@example.com',
                'notified' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];

            // put single row to the array of sample data
            array_push($data, $values);
        }

        return $data;
    }

    /**
     * Put all sample data to the DB.
     */
    protected function installSampleData()
    {
        $sampleData = $this->generateSampleData();

        foreach ($sampleData as $row) {
            $notification = $this->stockNotificationFactory->create();
            $notification
                ->addData($row)
                ->save();
        }
    }
}
