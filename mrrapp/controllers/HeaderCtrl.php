<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador para cambio de idioma.
 * Creado: Julio 5, 2017
 * Modificaciones: Cristian
 * Versión 1.0
 */
class HeaderCtrl extends MY_Controller
{
	/**
	 * changeLanguage
	 * Método que recibe el lenguaje que se quiere cambiar la pagina
	 */
	public function changeLanguage()
	{
		$lang = $this->input->post('lang',TRUE);
		$this->setLanguage($lang);
	}
}
