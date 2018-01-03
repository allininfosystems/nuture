<?php
namespace Magecomp\Gstcharge\Block\Order\Email\Items\Order;

use Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder as OrderDefualt;

class DefaultOrder extends OrderDefualt
{
    public function setTemplate($template) {
        return parent::setTemplate('Magecomp_Gstcharge::email/items/order/default.phtml');
    }
}