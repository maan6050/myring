<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		if(!isset($_SESSION['userId']) || !isset($_SESSION['userType']))  // El usuario no está logueado.
		{
			redirect(base_url('login'));
		}
		if($_SESSION['userType'] != ADMIN)  // El usuario no tiene permiso de ver este contenido.
		{
			redirect(base_url('home'));
		}
		$this->lang->load('recent_transactions_lang', $this->getLanguage());
	}

	/**
	 * contents
	 * Método que permite capturar las modificaciones de los contenidos mostrados en las RTE y actualizarlos en la base de datos.
	 * Además cuenta con un editor de texto enriquecido para almacenar los números de acceso pinless y tarifas.
	 */
	public function contents()
	{
		$this->lang->load('textEdit_lang', $this->getLanguage());
		$this->load->model('content');
		$data['title'] = lang('contents');
		$data['tab'] = $this->input->get_default('tab', 1);
		$contents = $this->content->getAll();
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			// El administrador desea actualizar los contenidos.
			foreach($contents as $value)
			{
				$text = $this->input->post($value->id.'-value', TRUE);
				$this->content->update($value->id, $text);
				$data[$value->id.'Title'] = lang($value->id.'Title');
				$data[$value->id.'Value'] = $text;
			}
			$data['msg'] = lang('msg_content_updated_sucessfully');
		}
		else
		{
			foreach($contents as $value)
			{
				$data[$value->id.'Title'] = lang($value->id.'Title');
				$data[$value->id.'Value'] = $value->content;
			}
		}
		$this->load->view('header', $data);
		$this->load->view('textEdit', $data);
		$this->load->view('footer');
	}

	/**
	 * countryEditForm
	 * Método que muestra el formulario de edición de un país.
	 */
	public function countryEditForm($id)
	{
		$this->load->model('country');
		$data['title'] = 'Edit country';
		$data['selCountry'] = $this->country->getById($id);
		$this->load->view('header', $data);
		$this->load->view('createCountry', $data);
		$this->load->view('footer');
	}

	public function countryEdit()
	{
		$this->load->model('country');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de actualización.
		{
			$items = $this->input->post(NULL, TRUE);
			$id = $items['id'];
			unset($items['id']);  // Quito el Id ya que no se va a actualizar.
			if($items['status'] == 'i')
			{
				// Si se inactivó el país, ya no es preferido.
				$items['preferred'] = 'n';
				// Borra la info de las tablas product y client_product.
				$this->country->deleteProducts($id);
			}
			$this->country->update($id, $items);  // Actualiza el país.
			$data['msg'] = 'The country was updated successfully.';
		}
		$data['countries'] = $this->country->getAll();
		$data['title'] = 'Countries list';
		$this->load->view('header', $data);
		$this->load->view('countries', $data);
		$this->load->view('footer');
	}

	public function countriesList()
	{
		$this->load->model('country');
		$data['countries'] = $this->country->getAll();
		$data['title'] = 'Countries list';
		$this->load->view('header', $data);
		$this->load->view('countries', $data);
		$this->load->view('footer');
	}

	/**
	 * productCreateForm
	 * Método que despliega el formulario para crear un producto.
	 */
	public function productCreateForm($countryId = NULL)
	{
		$this->load->model('country');
		$this->load->model('provider');
		$data['title'] = 'New product';
		$data['labels'] = array('action' => 'productCreate', 'image' => 'Image:', 'displayFixed' => '', 'displayRange' => ' style="display:none;"', 'btn' => 'Create');
		$data['providers'] = $this->provider->getAll();
		if(!is_null($countryId))
		{
			$country = $this->country->getById($countryId);
			$data['countryId'] = $countryId;
			$data['countryName'] = $country->name;
		}
		else
		{
			$data['countries'] = $this->country->getAll('a');
		}
		$this->load->view('header', $data);
		$this->load->view('createProduct', $data);
		$this->load->view('footer');
	}

	public function productCreate()
	{
		$this->load->model('product');
		$data['title'] = 'Products list';
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de creación.
		{
			$items = $this->input->post(NULL, TRUE);
			if($items['type'] == 'f')
			{
				$items['rangeMin'] = $items['rangeMax'] = NULL;
			}
			else
			{
				$items['fixed'] = NULL;  // Borro los datos de tarifas fijas si es por rango.
			}
			$data['countryId'] = $items['countryId'];
			$data['isUnlimited'] = $items['isUnlimited'];
			$data['msg'] = 'The product was created successfully.';
			// Decidió subir una imagen.
			if($_FILES['image']['name'] != '')
			{
				// Elimino los espacios en el nombre del archivo y lo paso a minúsculas.
				$imageName = strtolower(str_replace(' ', '-', $_FILES['image']['name']));
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
			$this->product->create($items);
		}
		else
		{
			$data['countryId'] = NULL;
		}

		$data['products'] = $this->product->getAll($data['countryId']);
		$this->load->view('header', $data);
		$this->load->view('products', $data);
		$this->load->view('footer');
	}

	public function productDelete($id, $countryId = NULL)
	{
		$id = (int)$id;
		$this->load->model('product');
		$product = $this->product->getById($id);
		if($this->product->delete($id))
		{
			if($product->image != '' && is_file(UPLOADS_DIR.$product->image))
			{
				@unlink(UPLOADS_DIR.$product->image);  // Elimino la imagen asociada.
			}
			$data['msg'] = 'The product was deleted successfully.';
		}
		$data['products'] = $this->product->getAll($countryId);
		$data['countryId'] = $countryId;
		$data['title'] = 'Products list';
		$this->load->view('header', $data);
		$this->load->view('products', $data);
		$this->load->view('footer');
	}

	public function productsList($countryId = NULL)
	{
		$this->load->model('product');
		$data['products'] = $this->product->getAll($countryId);
		$data['title'] = 'Products list';
		$data['countryId'] = $countryId;
		$this->load->view('header', $data);
		$this->load->view('products', $data);
		$this->load->view('footer');
	}

	public function productEditForm($id)
	{
		$this->load->model('product');
		$this->load->model('country');
		$this->load->model('provider');
		$data['title'] = 'Edit product';
		$data['labels'] = array('action' => 'productEdit', 'image' => 'Update image:', 'btn' => 'Update');
		$data['selProduct'] = $this->product->getById($id);
		$data['labels']['displayFixed'] = $data['selProduct']->type == 'r' ? ' style="display:none;"' : '';  // Si es rango escondo el fijo.
		$data['labels']['displayRange'] = $data['selProduct']->type == 'f' ? ' style="display:none;"' : '';  // Si es fijo escondo el rango.
		$country = $this->country->getById($data['selProduct']->countryId);
		$data['providers'] = $this->provider->getAll();
		$data['countryId'] = $country->id;
		$data['countryName'] = $country->name;
		$this->load->view('header', $data);
		$this->load->view('createProduct', $data);
		$this->load->view('footer');
	}

	public function productEdit()
	{
		$this->load->model('product');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de actualización.
		{
			$items = $this->input->post(NULL, TRUE);
			$data['countryId'] = $items['countryId'];
			$data['isUnlimited'] = $items['isUnlimited'];
			$data['includeCharge'] = $items['includeCharge'];
			$id = $items['id'];
			unset($items['id'], $items['countryId']);  // Quito el Id y el país ya que no se van a actualizar.
			$product = $this->product->getById($id);
			$data['msg'] = 'The product was updated successfully.';
			// Decidió subir otra imagen.
			if($_FILES['image']['name'] != '')
			{
				// Elimino los espacios en el nombre del archivo y lo paso a minúsculas.
				$imageName = strtolower(str_replace(' ', '-', $_FILES['image']['name']));
				// Obtengo la extensión del archivo.
				$ext = pathinfo($imageName, PATHINFO_EXTENSION);
				if(is_uploaded_file($_FILES['image']['tmp_name']) && ($ext == 'png' || $ext == 'jpg' || $ext == 'gif'))
				{
					// Borro la imagen anterior.
					if($product->image != '' && is_file(UPLOADS_DIR.$product->image))
					{
						@unlink(UPLOADS_DIR.$product->image);  // Elimino la imagen asociada.
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
			// Valido que solo queden las tarifas del tipo seleccionado.
			if($items['type'] == 'f')
			{
				$items['rangeMin'] = $items['rangeMax'] = NULL;
			}
			else
			{
				$items['fixed'] = NULL;  // Borro los datos de tarifas fijas si es por rango.
			}
			$this->product->update($id, $items);  // Actualiza el producto.
		}
		$data['products'] = $this->product->getAll($data['countryId']);
		$data['title'] = 'Products list';
		$this->load->view('header', $data);
		$this->load->view('products', $data);
		$this->load->view('footer');
	}

	/**
	 * providerCreateForm
	 * Método que despliega el formulario para crear un proveedor.
	 */
	public function providerCreateForm()
	{
		$data['title'] = 'New provider';
		$data['labels'] = array('action' => 'providerCreate', 'btn' => 'Create');
		$this->load->view('header', $data);
		$this->load->view('createProvider', $data);
		$this->load->view('footer');
	}

	public function providerCreate()
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de creación.
		{
			$items = $this->input->post(NULL, TRUE);
			$items['password'] = $this->encryption->encrypt($items['password']);
			$this->provider->create($items);
			$data['msg'] = 'The provider was created successfully.';
		}
		$data['providers'] = $this->provider->getAll();
		$data['title'] = 'Providers list';
		$this->load->view('header', $data);
		$this->load->view('providers', $data);
		$this->load->view('footer');
	}

	public function providerDelete($id)
	{
		$this->load->model('provider');
		if($this->provider->delete($id))
		{
			$data['msg'] = 'The provider was deleted successfully.';
		}
		$data['providers'] = $this->provider->getAll();
		$data['title'] = 'Providers list';
		$this->load->view('header', $data);
		$this->load->view('providers', $data);
		$this->load->view('footer');
	}

	public function providerEditForm($id)
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		$data['title'] = 'Provider user';
		$data['labels'] = array('action' => 'providerEdit', 'btn' => 'Update');
		$data['selProvider'] = $this->provider->getById($id);
		$data['selProvider']->password = $this->encryption->decrypt($data['selProvider']->password);
		$this->load->view('header', $data);
		$this->load->view('createProvider', $data);
		$this->load->view('footer');
	}

	public function providerEdit()
	{
		$this->load->model('provider');
		$this->load->library('encryption');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de actualización.
		{
			$items = $this->input->post(NULL, TRUE);
			$id = $items['id'];
			unset($items['id']);  // Quito el Id ya que no se va a actualizar.
			$items['password'] = $this->encryption->encrypt($items['password']);
			$this->provider->update($id, $items);  // Actualiza el proveedor.
			$data['msg'] = 'The provider was updated successfully.';
		}
		$data['providers'] = $this->provider->getAll();
		$data['title'] = 'Providers list';
		$this->load->view('header', $data);
		$this->load->view('providers', $data);
		$this->load->view('footer');
	}

	public function providersList()
	{
		$this->load->model('provider');
		$data['providers'] = $this->provider->getAll();
		$data['title'] = 'Providers list';
		$this->load->view('header', $data);
		$this->load->view('providers', $data);
		$this->load->view('footer');
	}

	public function sendSMS($phone, $msg)
	{
		// Envío el mensaje de texto.
		$this->load->library('twilio');
		$this->twilio->sendSMS($phone, urldecode($msg));
	}

	/**
	 * storeCreateForm
	 * Método que despliega el formulario para crear un negocio.
	 */
	public function storeCreateForm()
	{
		$this->load->model('user');
		$data['agents'] = $this->user->getAll(SELLER);
		$data['title'] = 'New store';
		$data['labels'] = array('action' => 'admin/storeCreate', 'pw' => 'Password:', 'req' => 'required', 'btn' => 'Create', 'maxBalance' => 'required');
		$this->load->view('header', $data);
		$this->load->view('createStore', $data);
		$this->load->view('footer');
	}

	public function storeCreate()
	{
		$this->load->model('client');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de creación.
		{
			$items = $this->input->post(NULL, TRUE);
			if(!$this->client->getByUsername($items['username']))
			{
				$items['type'] = STORE;
				$items['password'] = password_hash($items['password'], PASSWORD_DEFAULT);
				$this->client->create($items);
				$data['msg'] = 'The store was created successfully.';
			}
			else
			{
				$data['msg'] = 'A client with username '.$items['email'].' already exists.';
			}
		}
		$data['clients'] = $this->client->getAll(STORE);
		$data['title'] = 'Stores list';
		$data['clients'] = storesBalance($data['clients']);
		$data['controller'] = 'admin';
		$this->load->view('header', $data);
		$this->load->view('stores', $data);
		$this->load->view('footer');
	}

	public function storeDelete($id)
	{
		$id = (int)$id;
		$this->load->model('client');
		if($this->client->delete($id, STORE))
		{
			$data['msg'] = 'The store was deleted successfully.';
		}
		$data['clients'] = $this->client->getAll(STORE);
		$data['title'] = 'Stores list';
		$data['clients'] = storesBalance($data['clients']);
		$data['controller'] = 'admin';
		$this->load->view('header', $data);
		$this->load->view('stores', $data);
		$this->load->view('footer');
	}

	public function storeEditForm($id)
	{
		$id = (int)$id;
		$this->load->model('client');
		$this->load->model('user');
		$data['agents'] = $this->user->getAll(SELLER);
		$data['title'] = 'Edit store';
		$data['labels'] = array('action' => 'admin/storeEdit', 'pw' => 'New password: <em>optional</em>', 'req' => '', 'btn' => 'Update', 'maxBalance' => 'required');
		$data['selClient'] = $this->client->getById($id, STORE);
		$this->load->view('header', $data);
		$this->load->view('createStore', $data);
		$this->load->view('footer');
	}

	public function storeEdit()
	{
		$this->load->model('client');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de actualización.
		{
			$items = $this->input->post(NULL, TRUE);
			$id = $items['id'];
			unset($items['id']);  // Quito el Id ya que no se va a actualizar.
			if($items['password'] != '')
			{
				$items['password'] = password_hash($items['password'], PASSWORD_DEFAULT);
			}
			else
			{
				unset($items['password']);  // No va a cambiar la contraseña.
			}
			$this->client->update($id, $items);  // Actualiza el cliente.
			$data['msg'] = 'The store was updated successfully.';
		}
		$data['clients'] = $this->client->getAll(STORE);
		$data['title'] = 'Stores list';
		$data['clients'] = storesBalance($data['clients']);
		$data['controller'] = 'admin';
		$this->load->view('header', $data);
		$this->load->view('stores', $data);
		$this->load->view('footer');
	}

	public function storeProducts($clientId = NULL)
	{
		$this->load->model('client');
		$this->load->model('product');
		// Se envió el formulario de actualización.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$items = $this->input->post(NULL, TRUE);
			$products = $this->product->getAll(NULL);
			// Traigo el listado de porcentajes particulares para el cliente, a ver si alguno cambió.
			$clientProfit = $this->product->getClientProfit($clientId);
			// Recorro todos los productos para buscar cambios.
			foreach($products as $product)
			{
				foreach($items['profit'] as $id => $newProfit)
				{
					if($product->id == $id)
					{
						if($product->defaultProfit == $newProfit)
						{
							// Sí es el mismo valor por defecto que el del formulario,
							// entonces determino si tiene tarifa personalizada para eliminarla.
							foreach($clientProfit as $cp)
							{
								if($product->id == $cp->productId)
								{
									$this->product->deleteClientProfit($clientId, $product->id);
									// Dejo de buscar el ID pues ya lo encontré.
									break;
								}
							}
						}
						else
						{
							// Sí no es el mismo valor por defecto que el del formulario, entonces se reemplaza.
							$this->product->replaceClientProfit($clientId, $product->id, $newProfit);
						}
						break;
					}
				}
			}
			$data['msg'] = 'The fees were updated successfully.';
		}
		$data['client'] = $this->client->getById($clientId, STORE);
		$data['products'] = $this->product->getAll(NULL);
		// Traigo el listado de porcentajes particulares para el cliente.
		$clientProfit = $this->product->getClientProfit($clientId);
		foreach($data['products'] as &$product)
		{
			foreach($clientProfit as $cp)
			{
				// Si el cliente tiene un porcentaje particular, aquí reemplazo el que viene por defecto.
				if($product->id == $cp->productId)
				{
					$product->defaultProfit = $cp->profit;
					// No sigo buscando, avanzo al siguiente producto.
					break;
				}
			}
		}
		$data['title'] = 'Fee listing';
		$data['countryName'] = '';
		$this->load->view('header', $data);
		$this->load->view('storeProducts', $data);
		$this->load->view('footer');
	}

	public function storeProductsForm($clientId)
	{
		$this->load->model('client');
		$this->load->model('product');
		$data['client'] = $this->client->getById($clientId, STORE);
		$data['products'] = $this->product->getAll(NULL);
		// Traigo el listado de porcentajes particulares para el cliente.
		$clientProfit = $this->product->getClientProfit($clientId);
		foreach($data['products'] as &$product)
		{
			foreach($clientProfit as $cp)
			{
				// Si el cliente tiene un porcentaje particular, aquí reemplazo el que viene por defecto.
				if($product->id == $cp->productId)
				{
					$product->defaultProfit = $cp->profit;
					// No sigo buscando, avanzo al siguiente producto.
					break;
				}
			}
		}
		$data['title'] = 'Fee listing';
		$data['countryName'] = '';
		$this->load->view('header', $data);
		$this->load->view('storeProducts', $data);
		$this->load->view('footer');
	}

	public function storesList()
	{
		$this->load->model('client');
		// Se envió el formulario de búsqueda.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$name = $this->input->post('name', TRUE);
			$data['name'] = $name;
			$data['clients'] = $this->client->getAll(STORE, $name);
		}
		else
		{
			$data['clients'] = $this->client->getAll(STORE);
		}
		$data['title'] = 'Stores list';
		$data['clients'] = storesBalance($data['clients']);
		$data['controller'] = 'admin';
		$this->load->view('header', $data);
		$this->load->view('stores', $data);
		$this->load->view('footer');
	}

	/**
	 * sellerCreateForm
	 * Método que despliega el formulario para crear un agente de ventas.
	 */
	public function sellerCreateForm()
	{
		$data['title'] = 'New agent';
		$data['labels'] = array('action' => 'sellerCreate', 'pw' => 'Password:', 'req' => 'required', 'btn' => 'Create');
		$this->load->view('header', $data);
		$this->load->view('createSeller', $data);
		$this->load->view('footer');
	}

	public function sellerCreate()
	{
		$this->load->model('user');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de creación.
		{
			$items = $this->input->post(NULL, TRUE);
			if(!$this->user->getByEmail($items['email']))
			{
				$items['type'] = SELLER;
				$items['password'] = password_hash($items['password'], PASSWORD_DEFAULT);
				$this->user->create($items);
				$data['msg'] = 'The agent was created successfully.';
			}
			else
			{
				$data['msg'] = 'An agent with email '.$items['email'].' already exists.';
			}
		}
		$data['users'] = $this->user->getAll(SELLER);
		$data['title'] = 'Agents list';
		$this->load->view('header', $data);
		$this->load->view('sellers', $data);
		$this->load->view('footer');
	}

	public function sellerDelete($id)
	{
		$id = (int)$id;
		$this->load->model('user');
		if($this->user->delete($id, SELLER))
		{
			$data['msg'] = 'The agent was deleted successfully.';
		}
		$data['users'] = $this->user->getAll(SELLER);
		$data['title'] = 'Agents list';
		$this->load->view('header', $data);
		$this->load->view('sellers', $data);
		$this->load->view('footer');
	}

	public function sellerEditForm($id)
	{
		$id = (int)$id;
		$this->load->model('user');
		$data['title'] = 'Edit agent';
		$data['labels'] = array('action' => 'sellerEdit', 'pw' => 'New password: <em>optional</em>', 'req' => '', 'btn' => 'Update');
		$data['selUser'] = $this->user->getById($id, SELLER);
		$this->load->view('header', $data);
		$this->load->view('createSeller', $data);
		$this->load->view('footer');
	}

	public function sellerEdit()
	{
		$this->load->model('user');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de actualización.
		{
			$items = $this->input->post(NULL, TRUE);
			$id = $items['id'];
			unset($items['id']);  // Quito el Id ya que no se va a actualizar.
			if($items['password'] != '')
			{
				$items['password'] = password_hash($items['password'], PASSWORD_DEFAULT);
			}
			else
			{
				unset($items['password']);  // No va a cambiar la contraseña.
			}
			$this->user->update($id, $items);  // Actualiza el usuario.
			$data['msg'] = 'The agent was updated successfully.';
		}
		$data['users'] = $this->user->getAll(SELLER);
		$data['title'] = 'Agents list';
		$this->load->view('header', $data);
		$this->load->view('sellers', $data);
		$this->load->view('footer');
	}

	public function sellerProducts($sellerId = NULL)
	{
		$this->load->model('user');
		$this->load->model('product');
		// Se envió el formulario de actualización.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$items = $this->input->post(NULL, TRUE);
			$products = $this->product->getAll(NULL);
			// Traigo el listado de porcentajes particulares para el vendedor, a ver si alguno cambió.
			$sellerProfit = $this->product->getSellerProfit($sellerId);
			// Recorro todos los productos para buscar cambios.
			foreach($products as $product)
			{
				foreach($items['profit'] as $id => $newProfit)
				{
					if($product->id == $id)
					{
						if($product->defaultUserProfit == $newProfit)
						{
							// Sí es el mismo valor por defecto que el del formulario,
							// entonces determino si tiene tarifa personalizada para eliminarla.
							foreach($sellerProfit as $sp)
							{
								if($product->id == $sp->productId)
								{
									$this->product->deleteSellerProfit($sellerId, $product->id);
									// Dejo de buscar el ID pues ya lo encontré.
									break;
								}
							}
						}
						else
						{
							// Sí no es el mismo valor por defecto que el del formulario, entonces se reemplaza.
							$this->product->replaceSellerProfit($sellerId, $product->id, $newProfit);
						}
						break;
					}
				}
			}
			$data['msg'] = 'The fees were updated successfully.';
		}
		$data['seller'] = $this->user->getById($sellerId, SELLER);
		$data['products'] = $this->product->getAll(NULL);
		// Traigo el listado de porcentajes particulares para el vendedor.
		$sellerProfit = $this->product->getSellerProfit($sellerId);
		foreach($data['products'] as &$product)
		{
			foreach($sellerProfit as $sp)
			{
				// Si el vendedor tiene un porcentaje particular, aquí reemplazo el que viene por defecto.
				if($product->id == $sp->productId)
				{
					$product->defaultUserProfit = $sp->profit;
					// No sigo buscando, avanzo al siguiente producto.
					break;
				}
			}
		}
		$data['title'] = 'Fee listing';
		$data['countryName'] = '';
		$this->load->view('header', $data);
		$this->load->view('sellerProducts', $data);
		$this->load->view('footer');
	}

	public function sellerProductsForm($sellerId)
	{
		$this->load->model('user');
		$this->load->model('product');
		$data['seller'] = $this->user->getById($sellerId, SELLER);
		$data['products'] = $this->product->getAll(NULL);
		// Traigo el listado de porcentajes particulares para el vendedor.
		$sellerProfit = $this->product->getSellerProfit($sellerId);
		foreach($data['products'] as &$product)
		{
			foreach($sellerProfit as $sp)
			{
				// Si el vendedor tiene un porcentaje particular, aquí reemplazo el que viene por defecto.
				if($product->id == $sp->productId)
				{
					$product->defaultUserProfit = $sp->profit;
					// No sigo buscando, avanzo al siguiente producto.
					break;
				}
			}
		}
		$data['title'] = 'Fee listing';
		$data['countryName'] = '';
		$this->load->view('header', $data);
		$this->load->view('sellerProducts', $data);
		$this->load->view('footer');
	}

	public function sellersList()
	{
		$this->load->model('user');
		$data['users'] = $this->user->getAll(SELLER);
		$data['title'] = 'Agents list';
		$this->load->view('header', $data);
		$this->load->view('sellers', $data);
		$this->load->view('footer');
	}

	/**
	 * transactionsList
	 * Método que despliega el listado de las últimas transacciones realizadas.
	 */
	public function transactionsList($transactionId = NULL, $phone = NULL)
	{
		$this->load->model('client');
		$this->load->model('transaction');
		$data['title'] = 'Recent transactions';
		$data['controller'] = 'admin';
		$data['totalAmount'] = 0;
		$data['totalDue'] = 0;
		$data['totalFee'] = 0;
		$data['clients'] = $this->client->getStoresToInvoice();
		// El administrador desea borrar una transacción.
		if($transactionId != NULL && $phone != NULL)
		{
			$t = $this->transaction->getById($transactionId);
			if($t->phone == $phone)
			{
				$data['msg'] = 'The transaction was successfully deleted.';
				if($t->status == 'Success')
				{
					// Si la transacción es exitosa debo actualizar el balance antes de eliminarla. Se resta esto que debía.
					$due = ($t->amount + $t->serviceCharge - $t->profit) * -1;
					$this->client->updateBalance($t->clientId, $due);
					$data['msg'] .= ' $'.($due * -1).' was subtracted from the client´s balance.';
				}
				$this->transaction->delete($transactionId);
			}
			else
			{
				$data['msg'] = 'The phone '.$phone.' does not match the one in the transaction.';
			}
		}
		// Se envió el formulario de búsqueda.
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$from = $this->input->post('from', TRUE);
			$to = $this->input->post('to', TRUE);
			$status = $this->input->post('status', TRUE);
			$client = $this->input->post('client', TRUE);
			$data['from'] = $from;
			$data['to'] = $to;
			$data['status'] = $status;
			$data['client'] = $client;
			$data['transactions'] = $this->transaction->getAll($from, $to, $status, $client);
		}
		else
		{
			$data['from'] = $data['to'] = $data['client'] = '';
			$data['status'] = 'Success';
			$data['transactions'] = $this->transaction->getAll('', '', $data['status'], '');
		}
		foreach($data['transactions'] as &$t)
		{
			$realTopup = ($t->amount - $t->includeCharge);
			$t->amount = $realTopup + ($t->serviceCharge + $t->includeCharge);

			$t->balance = number_format($t->amount - $t->profit, 2);
			$t->product = $this->transaction->getProductName($t->productId);
			$t->providerId = $this->transaction->getProviderId($t->productId);
			$data['totalAmount'] += $t->amount;
			$data['totalDue'] += $t->balance;
			$data['totalFee'] += $t->profit;
		}
		$data['totalAmount'] = number_format($data['totalAmount'], 2);
		$data['totalDue'] = number_format($data['totalDue'], 2);
		$data['totalFee'] = number_format($data['totalFee'], 2);
		$this->load->view('header', $data);
		$this->load->view('clientTransactions', $data);
		$this->load->view('footer');
	}

	/**
	 * userCreateForm
	 * Método que despliega el formulario para crear un usuario.
	 */
	public function userCreateForm()
	{
		$data['title'] = 'New user';
		$data['labels'] = array('action' => 'userCreate', 'pw' => 'Password:', 'req' => 'required', 'btn' => 'Create');
		$this->load->view('header', $data);
		$this->load->view('createUser', $data);
		$this->load->view('footer');
	}

	public function userCreate()
	{
		$this->load->model('user');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de creación.
		{
			$items = $this->input->post(NULL, TRUE);
			if(!$this->user->getByEmail($items['email']))
			{
				$items['type'] = ADMIN;
				$items['password'] = password_hash($items['password'], PASSWORD_DEFAULT);
				$this->user->create($items);
				$data['msg'] = 'The user was created successfully.';
			}
			else
			{
				$data['msg'] = 'An user with email '.$items['email'].' already exists.';
			}
		}
		$data['users'] = $this->user->getAll(ADMIN);
		$data['title'] = 'Users list';
		$this->load->view('header', $data);
		$this->load->view('users', $data);
		$this->load->view('footer');
	}

	public function userDelete($id)
	{
		$id = (int)$id;
		$this->load->model('user');
		if($this->user->delete($id, ADMIN))
		{
			$data['msg'] = 'The user was deleted successfully.';
		}
		$data['users'] = $this->user->getAll(ADMIN);
		$data['title'] = 'Users list';
		$this->load->view('header', $data);
		$this->load->view('users', $data);
		$this->load->view('footer');
	}

	public function userEditForm($id)
	{
		$id = (int)$id;
		$this->load->model('user');
		$data['title'] = 'Edit user';
		$data['labels'] = array('action' => 'userEdit', 'pw' => 'New password: <em>optional</em>', 'req' => '', 'btn' => 'Update');
		$data['selUser'] = $this->user->getById($id, ADMIN);
		$this->load->view('header', $data);
		$this->load->view('createUser', $data);
		$this->load->view('footer');
	}

	public function userEdit()
	{
		$this->load->model('user');
		if($_SERVER['REQUEST_METHOD'] == 'POST')  // Se envió el formulario de actualización.
		{
			$items = $this->input->post(NULL, TRUE);
			$id = $items['id'];
			unset($items['id']);  // Quito el Id ya que no se va a actualizar.
			if($items['password'] != '')
			{
				$items['password'] = password_hash($items['password'], PASSWORD_DEFAULT);
			}
			else
			{
				unset($items['password']);  // No va a cambiar la contraseña.
			}
			$this->user->update($id, $items);  // Actualiza el usuario.
			$data['msg'] = 'The user was updated successfully.';
		}
		$data['users'] = $this->user->getAll(ADMIN);
		$data['title'] = 'Users list';
		$this->load->view('header', $data);
		$this->load->view('users', $data);
		$this->load->view('footer');
	}

	public function usersList()
	{
		$this->load->model('user');
		$data['users'] = $this->user->getAll(ADMIN);
		$data['title'] = 'Users list';
		$this->load->view('header', $data);
		$this->load->view('users', $data);
		$this->load->view('footer');
	}
}
