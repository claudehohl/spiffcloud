<?php
/**
 * Class and Function List:
 * Function list:
 * - generate()
 * - rgbhex()
 * - get_color_for_size()
 * - get_weighted_value()
 * Classes list:
 * - Tagcloud
 */

class Tagcloud
{
	
	function generate($words, $for_xml = false) 
	{

		// Remap array
		$cloud = array();
		foreach ($words as $index => $word) 
		{
			$cloud[$index]['word'] = $word['word'];
			$cloud[$index]['count'] = $word['c'];
			$cloud[$index]['timestamp'] = $word['timestamp'];
			$cloud[$index]['linked'] = $word['linked'];
		}

		// Find max
		$max = 0;
		foreach ($cloud as $value) 
		{
			
			if ($value['count'] > $max) 
			{
				$max = $value['count'];
			}
		}

		// Calculate sizes
		
		if ($for_xml) 
		{
			$scala_min = 1;
			$scala_max = 8;
		}
		else
		{
			$scala_min = 7;
			$scala_max = 40;
		}
		foreach ($cloud as $index => $value) 
		{
			
			if ($value['linked'] == true) 
			{
				$color = 'green';
			}
			else
			{
				$color = 'blue';
			}
			$cloud[$index]['size'] = $this->get_weighted_value($value['count'], '1', $max, $scala_min, $scala_max);
			$cloud[$index]['color'] = $this->get_color_for_size($color, $cloud[$index]['size'], $scala_min, $scala_max);
		}
		return $cloud;
	}
	
	function rgbhex($red, $green, $blue) 
	{
		return sprintf('%02X%02X%02X', $red, $green, $blue);
	}
	
	function get_color_for_size($color, $size, $scala_min, $scala_max) 
	{

		// 4572f3
		// Green: 70, 115, 0

		// Blue: 70, 115, 220

		// normal: $this->get_weighted_value($size, $scala_min, $scala_max, '0', '220');

		// reverse: $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '0', '220');

		$green_r = $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '70', '160');
		$green_g = $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '160', '160');
		$green_b = $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '0', '160');
		$green = $this->rgbhex($green_r, $green_g, $green_b);
		$blue_r = $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '70', '200');
		$blue_g = $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '140', '200');
		$blue_b = $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '200', '200');
		$blue = $this->rgbhex($blue_r, $blue_g, $blue_b);
		$grey_r = $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '70', '180');
		$grey_g = $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '70', '180');
		$grey_b = $this->get_weighted_value($scala_max - $size + $scala_min, $scala_min, $scala_max, '70', '180');
		$grey = $this->rgbhex($grey_r, $grey_g, $grey_b);
		
		if ($color == 'green') 
		{
			return $green;
		}
		
		if ($color == 'blue') 
		{
			return $blue;
		}
		
		if ($color == 'grey') 
		{
			return $grey;
		}
	}
	
	function get_weighted_value($value, $min, $max, $scala_min, $scala_max) 
	{
		
		if ($max == 1) 
		{
			$max = 2;
			$value = 2;
		}
		$diff_max_min = $max - $min;
		$diff_max_value = $max - $value;
		$prop = ($diff_max_min - $diff_max_value) / $diff_max_min;
		$size = (($scala_max - $scala_min) * $prop) + $scala_min;
		return round($size, 2);
	}
}
