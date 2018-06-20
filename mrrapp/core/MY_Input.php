<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Input extends CI_Input
{
	function get_default($index = NULL, $default_value = NULL, $xss_clean = TRUE)
	{
		if($index === NULL && !empty($_GET))
		{
			$get = array();
			foreach(array_keys($_GET) as $key)
			{
				$get[$key] = $this->_fetch_from_array($_GET, $key, $xss_clean);
			}
			return $get;
		}
		$ret_val = $this->_fetch_from_array($_GET, $index, $xss_clean);
		if(!$ret_val && empty($ret_val))
		{
			$ret_val = $default_value;
		}
		return $ret_val;
	}

	function post_default($index = NULL, $default_value = NULL, $xss_clean = TRUE)
	{
		if($index === NULL && !empty($_POST))
		{
			$post = array();
			foreach(array_keys($_POST) as $key)
			{
				$post[$key] = $this->_fetch_from_array($_POST, $key, $xss_clean);
			}
			return $post;
		}
		$ret_val = $this->_fetch_from_array($_POST, $index, $xss_clean);
		if(!$ret_val && empty($ret_val))
		{
			$ret_val = $default_value;
		}
		return $ret_val;
	}
}
