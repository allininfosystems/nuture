<?php

namespace Bluethink\Dailydose\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Socal implements ArrayInterface
{
   const TWITTER = 'Twitter';
   const FACEBOOK = 'Facebook';
   const INSTAGRAM = 'Instagram';

   /**
    * @return array
    */
   public function toOptionArray()
   {
       $options = [
           self::TWITTER => __('Twitter'),
           self::FACEBOOK => __('Facebook'),
           self::INSTAGRAM => __('Instagram')
       ];

       return $options;
   }
}