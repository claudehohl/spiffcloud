<?php
/**
 * Class and Function List:
 * Function list:
 * - space_name()
 * Classes list:
 */

function space_name($string) 
{
	$CI = & get_instance();

	//replace ä ö ü
	$string = convert_accented_characters($string);

	//replace not allowed chars
	$count = strlen($string);
	$result = '';
	for ($i = 0;$i < $count;$i++) 
	{
		
		if (preg_match('{[' . $CI->config->item('permitted_uri_chars') . ']}', $string[$i])) 
		{
			$result.= $string[$i];
		}
	}

	//
	return $result;
}
