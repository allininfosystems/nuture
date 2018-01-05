<?php


/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

namespace WIO\Forum\Helper;

class Dates extends \Magento\Framework\App\Helper\AbstractHelper {
  
  protected $_timezoneInterface;
  
  public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
  ) {
    parent::__construct($context);
    $this->_timezoneInterface = $timezoneInterface;
  } 
  
  public function getTimeAccordingToTimeZone($dateTime) {
    if(!$dateTime) {
      return '-';
    }
    $dateTimeAsTimeZone = $this->_timezoneInterface
            ->date(new \DateTime($dateTime))
            ->format('m/d/y H:i:s');
    return $dateTimeAsTimeZone;
  }
}
