<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		if(isset($_SESSION['userId']))  // El usuario ya inició sesión.
		{
			redirect(base_url('home'));
		}
		else
		{
			$data['title'] = 'Login';
			$this->load->view('login', $data);
		}
	}

	public function authenticate()
	{
		$error = FALSE;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('un', 'Username', 'required');
		$this->form_validation->set_rules('pw', 'Password', 'required');
		if($this->form_validation->run() !== FALSE)
		{
			$items = $this->input->post(NULL, TRUE);
		
			$this->load->model('user');
		
			$row = $this->user->getByEmail($items['un']);  // Primero busco si es un administrador.
		
			if($row != NULL)
			{
				if(password_verify($items['pw'], $row->password))
				{
					$_SESSION['userId'] = $row->id;
					$_SESSION['userName'] = $row->name;
					$_SESSION['userType'] = $row->type;
					redirect(base_url('home'));
				}
				else
				{
					$error = TRUE;
					$data['un'] = $this->input->post('un');
					$data['error'] = 'Invalid Username or Password.';
				}
			}
			else
			{
				// No lo encontró como administrador, busco si es un cliente.
				$this->load->model('client');
				$row = $this->client->getByUsername($items['un']);  // Ahora busco si es un cliente.
				if($row != NULL)
				{
					if(password_verify($items['pw'], $row->password))
					{
						$_SESSION['userId'] = $row->id;
						$_SESSION['userName'] = $row->name;
						$_SESSION['userType'] = $row->type;
						redirect(base_url('home'));
					}
					else
					{
						$error = TRUE;
						$data['un'] = $this->input->post('un');
						$data['error'] = 'Invalid Username or Password.';
					}
				}
				else
				{
					// Tampoco es un cliente.
					$error = TRUE;
					$data['un'] = $this->input->post('un');
					$data['error'] = 'Invalid Username or Password.';
				}
			}
		}
		else
		{
			$error = TRUE;
			$data['un'] = $this->input->post('un');
			$data['error'] = validation_errors();
		}
		if($error)
		{
			$data['title'] = 'Login';
			$this->load->view('login', $data);
		}
	}

	public function generate($str)
	{
		$hash = password_hash($str, PASSWORD_DEFAULT);
		echo '<pre>'.$str.' => '.$hash.'</pre>';
	}
}
