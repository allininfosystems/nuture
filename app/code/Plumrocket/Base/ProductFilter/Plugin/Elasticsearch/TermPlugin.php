<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ProductFilter\Plugin\Elasticsearch;

class TermPlugin
{
	public function afterBuildFilter(\Magento\Elasticsearch\SearchAdapter\Filter\Builder\Term $subject, $result)
    {
    	foreach ($result as $i => $ritems) {
	        foreach ($ritems as $operator => $items) {
	        	//if ($operator == 'terms') 
	        	foreach ($items as $key => $value) {
	        		if (is_array($value)) {
	        			if (count($value) == 1 && array_key_exists('in', $value)) {
	        				$result[$i][$operator][$key] = $value['in'];
	        			}
	        		}
	        	}
	        }
	    }

        return $result;
    }
}