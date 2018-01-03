<?php
namespace Magestore\Rewardpoints\Model\Plugin\CustomerData;
class Cart  {
    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $helperPoint;

    protected $_calculationEarning;

    public function __construct(
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Helper\Calculation\Earning $calculationEarning
    )
    {
        $this->helperPoint = $helperPoint;
        $this->_calculationEarning = $calculationEarning;
    }
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result) {
        $earningPoint =  $this->_calculationEarning->getTotalPointsEarning();
        $earningPointFormat = $this->helperPoint->format($earningPoint);
        if ($earningPointFormat) {
            $result['earnPoint'] = $earningPointFormat;
        } else {
            $result['earnPoint'] = false;
        }
        return $result;
    }

}