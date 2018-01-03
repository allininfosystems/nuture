<?php
/**
 * Webinse
 *
 * PHP Version 5.6.23
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */
/**
 * Resource Model for the History of created barcodes.
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */

namespace Webinse\Barcode\Model\ResourceModel;

class History extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('webinse_barcode_history_barcodes', \Webinse\Barcode\Api\Data\HistoryInterface::ID);
    }
}