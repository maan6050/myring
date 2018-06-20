<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		if(!isset($_SESSION['userId']) || !isset($_SESSION['userType']))
		{
			// El usuario no está logueado.
			redirect(base_url('login'));
		}
		if($_SESSION['userType'] != ADMIN)
		{
			// El usuario no tiene permiso de ver este contenido.
			redirect(base_url('home'));
		}
		$this->lang->load('header_lang', $this->getLanguage());
	}

	public function newsCreateForm()
	{
		$data['title'] = 'Create news';
		$data['labels'] = array('action' => 'newsCreate', 'image' => 'Image:', 'btn' => 'Create');
		$this->load->view('header', $data);
		$this->load->view('createNews', $data);
		$this->load->view('footer');
	}

	public function newsCreate()
	{
		$this->load->model('news');
		$data['title'] = 'News list';
		// Se envió el formulario de creación.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$items = $this->input->post(NULL, TRUE);
			$data['msg'] = 'The news was created successfully.';
			// Decidió subir una imagen.
			if($_FILES['image']['name'] != '')
			{
				// Adiciono un prefijo.
				$imageName = 'news-';
				// Elimino los espacios en el nombre del archivo y lo paso a minúsculas.
				$imageName .= strtolower(str_replace(' ', '-', $_FILES['image']['name']));
				// Obtengo la extensión del archivo.
				$ext = pathinfo($imageName, PATHINFO_EXTENSION);
				if(is_uploaded_file($_FILES['image']['tmp_name']) && ($ext == 'png' || $ext == 'jpg' || $ext == 'gif'))
				{
					if(move_uploaded_file($_FILES['image']['tmp_name'], UPLOADS_DIR.$imageName))
					{
						$items['image'] = $imageName;
					}
					else
					{
						$data['msg'] .= ' But the image cannot be uploaded.';
					}
				}
				else
				{
					$data['msg'] .= ' But the image extension isn\\\'t png, jpg or gif.';
				}
			}
			$this->news->create($items);
		}

		$data['news'] = $this->news->getAll();
		$this->load->view('header', $data);
		$this->load->view('news', $data);
		$this->load->view('footer');
	}

	public function newsDelete($id)
	{
		$id = (int)$id;
		$this->load->model('news');
		$news = $this->news->getById($id);
		if($this->news->delete($id))
		{
			if($news->image != '' && is_file(UPLOADS_DIR.$news->image))
			{
				// Elimino la imagen asociada.
				@unlink(UPLOADS_DIR.$news->image);
			}
			$data['msg'] = 'The news was deleted successfully.';
		}
		$data['news'] = $this->news->getAll();
		$data['title'] = 'News list';
		$this->load->view('header', $data);
		$this->load->view('news', $data);
		$this->load->view('footer');
	}

	public function newsEditForm($id)
	{
		$this->load->model('news');
		$data['title'] = 'Edit news';
		$data['labels'] = array('action' => 'newsEdit', 'image' => 'Update image:', 'btn' => 'Update');
		$data['selNews'] = $this->news->getById($id);
		$this->load->view('header', $data);
		$this->load->view('createNews', $data);
		$this->load->view('footer');
	}

	public function newsEdit()
	{
		$this->load->model('news');
		// Se envió el formulario de actualización.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$items = $this->input->post(NULL, TRUE);
			$id = $items['id'];
			// Quito el Id ya que no se va a actualizar.
			unset($items['id']);
			$news = $this->news->getById($id);
			$data['msg'] = 'The news was updated successfully.';
			// Decidió subir otra imagen.
			if($_FILES['image']['name'] != '')
			{
				// Adiciono un prefijo.
				$imageName = 'news-';
				// Elimino los espacios en el nombre del archivo y lo paso a minúsculas.
				$imageName .= strtolower(str_replace(' ', '-', $_FILES['image']['name']));
				// Obtengo la extensión del archivo.
				$ext = pathinfo($imageName, PATHINFO_EXTENSION);
				if(is_uploaded_file($_FILES['image']['tmp_name']) && ($ext == 'png' || $ext == 'jpg' || $ext == 'gif'))
				{
					// Borro la imagen anterior.
					if($news->image != '' && is_file(UPLOADS_DIR.$news->image))
					{
						// Elimino la imagen asociada.
						@unlink(UPLOADS_DIR.$news->image);
					}
					if(move_uploaded_file($_FILES['image']['tmp_name'], UPLOADS_DIR.$imageName))
					{
						$items['image'] = $imageName;
					}
					else
					{
						$data['msg'] .= ' But the image cannot be uploaded.';
					}
				}
				else
				{
					$data['msg'] .= ' But the image extension isn\\\'t png, jpg or gif.';
				}
			}
			$this->news->update($id, $items);
		}
		$data['news'] = $this->news->getAll();
		$data['title'] = 'News list';
		$this->load->view('header', $data);
		$this->load->view('news', $data);
		$this->load->view('footer');
	}

	public function newsList()
	{
		$this->load->model('news');
		// Se envió el formulario de búsqueda.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$searchNews = $this->input->post('searchNews', TRUE);
			$data['searchNews'] = $searchNews;
			$data['news'] = $this->news->getAll($searchNews);
		}
		else
		{
			$data['news'] = $this->news->getAll();
		}
		$data['title'] = 'News list';
		$this->load->view('header', $data);
		$this->load->view('news', $data);
		$this->load->view('footer');
	}

	/**
	 * Gestión de imágenes del carrusel.
	 */
	public function slideCreateForm()
	{
		$data['title'] = 'Create slide';
		$this->load->view('header', $data);
		$this->load->view('createSlide', $data);
		$this->load->view('footer');
	}

	public function slideCreate()
	{
		$this->load->model('slide');
		$data['title'] = 'Slides list';
		// Se envió el formulario de creación.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// Verifico que haya seleccionado una imagen.
			if($_FILES['image']['name'] != '')
			{
				// Adiciono un prefijo.
				$imageName = 'slide-';
				// Elimino los espacios en el nombre del archivo y lo paso a minúsculas.
				$imageName .= strtolower(str_replace(' ', '-', $_FILES['image']['name']));
				// Obtengo la extensión del archivo.
				$ext = pathinfo($imageName, PATHINFO_EXTENSION);
				if(is_uploaded_file($_FILES['image']['tmp_name']) && ($ext == 'png' || $ext == 'jpg' || $ext == 'gif'))
				{
					if(move_uploaded_file($_FILES['image']['tmp_name'], UPLOADS_DIR.$imageName))
					{
						$item = array('image' => $imageName);
						$this->slide->create($item);
						$data['msg'] = 'The slide was created successfully.';
					}
					else
					{
						$data['msg'] = 'The image cannot be uploaded.';
					}
				}
				else
				{
					$data['msg'] = 'The image extension isn\\\'t png, jpg or gif.';
				}
			}
		}

		$data['slides'] = $this->slide->getAll();
		$this->load->view('header', $data);
		$this->load->view('slides', $data);
		$this->load->view('footer');
	}

	public function slideDelete($id)
	{
		$id = (int)$id;
		$this->load->model('slide');
		$slide = $this->slide->getById($id);
		if($this->slide->delete($id))
		{
			if($slide->image != '' && is_file(UPLOADS_DIR.$slide->image))
			{
				// Elimino la imagen asociada.
				@unlink(UPLOADS_DIR.$slide->image);
			}
			$data['msg'] = 'The image was deleted successfully.';
		}
		$data['slides'] = $this->slide->getAll();
		$data['title'] = 'Slides list';
		$this->load->view('header', $data);
		$this->load->view('slides', $data);
		$this->load->view('footer');
	}

	public function slidesList()
	{
		$this->load->model('slide');
		// Se envió el formulario de búsqueda.
		$data['slides'] = $this->slide->getAll();
		$data['title'] = 'Slides list';
		$this->load->view('header', $data);
		$this->load->view('slides', $data);
		$this->load->view('footer');
	}
}
