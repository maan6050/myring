<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	private $lang_allowed = array('en', 'es');

	public function __construct()
	{
		parent::__construct();

		$this->lang->load('header', $this->getLanguage());

		$this->load->helper('general');
		$this->load->helper('language');
		$this->load->helper('url');
	}

	public function getLanguage()
	{
		if(!$this->session->userdata('lang'))
		{
			$this->session->set_userdata('lang', 'en');
			return 'en';
		}
		else
		{
			$lang = $this->session->userdata('lang');
			return in_array($lang, $this->lang_allowed) ? $lang : 'en';
		}
	}

	public function setLanguage($lang)
	{
		$currentUrl = base_url($this->uri->uri_string());
		$currentUrl = str_replace(array('/setLanguage/en', '/setLanguage/es'), '', $currentUrl);

		$this->session->set_userdata('lang', in_array($lang, $this->lang_allowed) ? $lang : 'en');
		redirect($currentUrl, 'refresh');
	}
}
