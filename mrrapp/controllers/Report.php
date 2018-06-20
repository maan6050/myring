<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador para reportes
 * Creado: Junio 30, 2017
 * Modificaciones: Cristian, CZapata
 * Versión 1.0
 */
class Report extends MY_Controller
{
	/**
	 * Método constructor con validación de login.
	 */
	public function __construct()
	{
		parent::__construct();
		if(!isset($_SESSION['userId']) || !isset($_SESSION['userType']))  // El usuario no está logueado.
		{
			redirect(base_url('login'));
		}
		$this->load->model('client');
		$this->load->model('deposit');
		$this->load->model('product');
		$this->load->model('transaction');
		$this->load->model('user');
		$this->lang->load('invoices_lang', $this->getLanguage());
	}

	/**
	 * companyEarnings
	 * Reporte que muestra el listado de ganancias de la empresa, agrupadas por tienda o producto.
	 */
	public function companyEarnings()
	{
		if($_SESSION['userType'] == ADMIN)  // Si el usuario es administrador.
		{
			$data['title'] = 'Company earnings';
			$data['totalAmount'] = $data['totalIncluded'] = $data['totalClientEarnings'] = $data['totalUserEarnings'] = $data['totalEarnings'] = $data['totalNetProfit'] = 0;

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$from = $this->input->post('from', TRUE);
				$to = $this->input->post('to', TRUE);
				$groupedBy = $this->input->post('groupedBy', TRUE);
				$data['from'] = $from;
				$data['to'] = $to;
				$data['groupedBy'] = $groupedBy;
				$data['earnings'] = $this->transaction->getEarnings($from, $to, $groupedBy);
			}
			else
			{
				// Por defecto calculo las ganancias de este mes.
				$data['from'] = date('Y-m-01');
				$data['to'] = date('Y-m-d');
				$data['groupedBy'] = 'clientId';
				$data['earnings'] = $this->transaction->getEarnings($data['from'], $data['to'], $data['groupedBy']);
			}
			// Sumo los valores para el gran total.
			foreach($data['earnings'] as &$earning)
			{
				$earning->netProfit = round($earning->earnings + $earning->included - $earning->clientEarnings - $earning->userEarnings, 2);
				$earning->class = $earning->netProfit <= 0 ? 'red' : '';
				$data['totalAmount'] += $earning->total;
				$data['totalIncluded'] += $earning->included;
				$data['totalEarnings'] += $earning->earnings + $earning->included;
				$data['totalNetProfit'] += $earning->netProfit;
			}

			$this->load->view('header', $data);
			$this->load->view('companyEarnings', $data);
			$this->load->view('footer');
		}
		else
		{
			redirect(base_url('home'));
		}
	}

	/**
	 * recentDeposit
	 * Reporte que muestra el listado de los últimos depósitos de las tiendas.
	 * En el caso de ser una tienda, el método mostrará la vista my payments.
	 */
	public function recentDeposit()
	{
		if($_SESSION['userType'] == STORE)
		{
			$data['title'] = lang('my_payments');
			$this->lang->load('report', $this->getLanguage());
			$store = $_SESSION['userId'];
			$data['store'] = $store;
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$from = $this->input->post('from', TRUE);
				$to = $this->input->post('to', TRUE);
				$data['from'] = $from;
				$data['to'] = $to;
				$data['deposits'] = $this->deposit->getByDates($from, $to, $store);
			}
			else
			{
				$data['from'] = $data['to'] = '';
				$data['deposits'] = $this->deposit->getByDates('', '', $store);
			}
			$this->load->view('header', $data);
			$this->load->view('myPayments', $data);
			$this->load->view('footer');
		}
		else
		{
			$data['title'] = lang('recent_deposits');
			$data['stores'] = $this->client->getStores();

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$from = $this->input->post('from', TRUE);
				$to = $this->input->post('to', TRUE);
				$store = $this->input->post('store', TRUE);
				$data['from'] = $from;
				$data['to'] = $to;
				$data['store'] = $store;
				$data['deposits'] = $this->deposit->getByDates($from, $to, $store);
			}
			else
			{
				$data['from'] = $data['to'] = $data['store'] = '';
				$data['deposits'] = $this->deposit->getByDates('', '', '');
			}

			$this->load->view('header', $data);
			$this->load->view('recentDeposits', $data);
			$this->load->view('footer');
		}
	}

	/**
	 * reconcile
	 * Reporte que calcula cuáles clientes tienen mal su balance.
	 */
	public function reconcile($id = '')
	{
		if($_SESSION['userType'] == ADMIN)  // Si el usuario es administrador.
		{
			$data['stores'] = $this->client->getAll(STORE);
			$data['title'] = 'Reconcile balance';
			foreach($data['stores'] as $key => &$store)
			{
				// Todas las transacciones desde que se creó el sistema hasta la fecha.
				$transaction = $this->transaction->getSumByClientId($store->id);
				$store->deposits = $this->deposit->sumClientDeposits($store->id);
				$client = $this->client->getById($store->id, STORE);
				$store->balance = round($client->balance, 2);
				$store->totalDue = $transaction->amount + $transaction->serviceCharge - $transaction->profit;
				$store->realBalance = round($store->totalDue - $store->deposits, 2);
				$store->transactions = $transaction->transactions;
				if($id != '' && $id == $store->id)
				{
					$id = (int)$id;
					if($this->client->changeBalance($id, $store->realBalance))
					{
						$data['msg'] = 'The balance was changed successfully.';
						$store->balance = $store->realBalance;
					}
				}
				if($store->realBalance == $store->balance)
				{
					// Elimino las tiendas que no tienen balance o donde concuerda.
					unset($data['stores'][$key]);
				}
			}
			$this->load->view('header', $data);
			$this->load->view('reconcile', $data);
			$this->load->view('footer');
		}
		else
		{
			redirect(base_url('home'));
		}
	}

	/**
	 * Excel
	 * Método que me genera un archivo de Excel.
	 */
	public function Excel($date, $store)
	{
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		// Name the worksheet.
		$this->excel->getActiveSheet()->setTitle('Report');
		// Set cell A1 content with some logo.
		$gdImage = imagecreatefromjpeg(base_url('images/logo-myringring.jpg'));
		$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
		$objDrawing->setName('PHPExcel image');
		$objDrawing->setDescription('PHPExcel image');
		$objDrawing->setImageResource($gdImage);
		$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
		$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG);
		$objDrawing->setWidthAndHeight(80, 180);
		$objDrawing->setCoordinates('A2');
		$objDrawing->setWorksheet($this->excel->getActiveSheet());
		$this->excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
		// Set table headers for the report
		$this->excel->getActiveSheet()->setCellValue('A1', lang('report'));
		$this->excel->getActiveSheet()->setCellValue('A9', lang('col_date'));
		$this->excel->getActiveSheet()->setCellValue('B9', lang('col_transId'));
		$this->excel->getActiveSheet()->setCellValue('C9', lang('col_store'));
		$this->excel->getActiveSheet()->setCellValue('D9', lang('col_product'));
		$this->excel->getActiveSheet()->setCellValue('E9', lang('col_client_phone'));
		$this->excel->getActiveSheet()->setCellValue('F9', lang('col_phone'));
		$this->excel->getActiveSheet()->setCellValue('G9', 'Pin');
		$this->excel->getActiveSheet()->setCellValue('H9', lang('col_status'));
		$this->excel->getActiveSheet()->setCellValue('I9', lang('col_amount'));
		$this->excel->getActiveSheet()->setCellValue('J9', lang('col_service_charge'));
		$this->excel->getActiveSheet()->setCellValue('K9', lang('col_include_charge'));
		$this->excel->getActiveSheet()->setCellValue('L9', lang('col_profit'));
		$this->excel->getActiveSheet()->setCellValue('M9', 'Total');
		// Merge cell.
		$this->excel->getActiveSheet()->mergeCells('A1:M1');
		$this->excel->getActiveSheet()->getColumnDimension("K")->setAutoSize(true);
		// Set aligment to center for that merged cell (A1 to C1).
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		// Make the font become bold.
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(24);
		$this->excel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('#333');

		for($col=ord('A'); $col<=ord('M'); $col++)
		{
			// Set column dimension.
			$this->excel->getActiveSheet()->getColumnDimension(chr($col))->setAutoSize(true);
			// Change the font size.
			$this->excel->getActiveSheet()->getStyle(chr($col))->getFont()->setSize(12);
			$this->excel->getActiveSheet()->getStyle(chr($col))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		// Query.
		$rs = $this->transaction->getSpecialInvoice($date, $store);
		$exceldata = '';
		foreach($rs->result_array() as $row)
		{
			$exceldata[] = $row;
		}
		// Fill data.
		$num_rows = count($exceldata);
		$totals_row = $num_rows + 10;
		$endrow_sum = $totals_row - 1;
		$this->excel->getActiveSheet()->fromArray($exceldata, null, 'A10');

		$cdata = $this->client->getById($store, STORE);
		$this->excel->getActiveSheet()->setCellValue('J2', 'Bill To');
		$this->excel->getActiveSheet()->setCellValue('J3', $cdata->name);
		$this->excel->getActiveSheet()->setCellValue('J4', $cdata->address);
		$this->excel->getActiveSheet()->setCellValue('J5', $cdata->city.','.$cdata->state.','.$cdata->country);
		$this->excel->getActiveSheet()->setCellValue('J6', 'Tel: '.$cdata->phone);
		$this->excel->getActiveSheet()->setCellValue('J7', 'Contact: '.$cdata->contactName);
		for($i=2;$i<8;$i++)
		{
			$this->excel->getActiveSheet()->mergeCells("J$i:M$i");
			// Change the font size.
			$this->excel->getActiveSheet()->getStyle("J$i")->getFont()->setSize(12);
			$this->excel->getActiveSheet()->getStyle("J$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		}
		$this->excel->getActiveSheet()->getStyle('J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$styleArray = array(
			'borders' => array(
				'outline' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('rgb' => '000000'),
				),
			),
		);
		$this->excel->getActiveSheet()->getStyle('J2:M7')->applyFromArray($styleArray);
		$this->excel->getActiveSheet()->getStyle('J2:M2')->applyFromArray($styleArray);

		$this->excel->getActiveSheet()->getStyle('A10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('B10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('C10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('D10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('E10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('F10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('G10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('H10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('I10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('J10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('K10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('L10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('M10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// Totals
		$this->excel->getActiveSheet()->mergeCells("A$totals_row:H$totals_row");
		$this->excel->getActiveSheet()->getStyle('A'.$totals_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$this->excel->getActiveSheet()->setCellValue('A'.$totals_row, 'Total');
		$this->excel->getActiveSheet()->setCellValue('I'.$totals_row,'=SUM(I10:I'.$endrow_sum.')');
		$this->excel->getActiveSheet()->setCellValue('J'.$totals_row,'=SUM(J10:J'.$endrow_sum.')');
		$this->excel->getActiveSheet()->setCellValue('K'.$totals_row,'=SUM(K10:K'.$endrow_sum.')');
		$this->excel->getActiveSheet()->setCellValue('L'.$totals_row,'=SUM(L10:L'.$endrow_sum.')');
		$this->excel->getActiveSheet()->setCellValue('M'.$totals_row,'=SUM(M10:M'.$endrow_sum.')');

		$filename = 'Recent_deposit_report-'.date('d/m/y').'.xls';  // Save our workbook as this file name.
		header('Content-Type: application/vnd.ms-excel');  // mime type.
		header('Content-Disposition: attachment;filename="'.$filename.'"');  // Tell browser what's the file name.
		header('Cache-Control: max-age=0');  // No cache.
		// Save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		// if you want to save it as .XLSX Excel 2007 format.
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		// Force user to download the Excel file without writing it to server's HD.
		$objWriter->save('php://output');
	}

	/**
	 * generateInvoice
	 * Método que se encarga de generar la factura, ya sea pdf o excel.
	 */
	public function generateInvoice()
	{
		$date = $this->input->get('date', TRUE);
		$store = $this->input->get('sto', TRUE);
		if(isset($_GET['excel']))
		{
			$this->Excel($date, $store);
		}
		else
		{
			$this->pdf($date, $store);
		}
	}

	/**
	 * guestsNumbers
	 * Método que despliega un listado con los teléfonos que contengan el atributo clientphone 1111111111 para su eliminación.
	 */
	public function guestsNumbers()
	{
		$this->load->model('phonebook');
		$clientPhone = '1111111111';  // clientPhone por defecto.
		$data['title'] = lang('guests_numbers');
		$data['phones'] = $this->phonebook->getAll($clientPhone);
		$this->load->view('header', $data);
		$this->load->view('guestsNumber', $data);
		$this->load->view('footer');
	}

	/**
	 * guestNumberDelete
	 * Método que permite eliminar un número seleccionado por sus llaves.
	 * @param type $phone variable con el valor de la llave phone.
	 * @param type $pro variable con el valor de la llave productId.
	 */
	public function guestNumberDelete($phone, $pro)
	{
		$pho = $phone;
		$proId = $pro;

		$clientP = '1111111111';  // clientPhone por defecto.
		$this->load->model('phonebook');
		if($this->phonebook->delete($clientP, $pho, $proId))
		{
			$data['msg'] = 'The phone was deleted successfully.';
		}
		$data['title'] = 'Guest´s Numbers';
		$data['phones'] = $this->phonebook->getAll($clientP);
		$this->load->view('header', $data);
		$this->load->view('guestsNumber', $data);
		$this->load->view('footer');
	}

	/**
	 * guestsNumbersDeleteAll
	 * Método que elimina todos los números con el clientphone 1111111111.
	 */
	public function guestsNumbersDeleteAll()
	{
		$clientP = '1111111111';  // ClientPhone por defecto.
		$this->load->model('phonebook');
		if($this->phonebook->deleteByClientP($clientP))
		{
			$data['msg'] = 'All phones were deleted successfully.';
		}
		$data['title'] = 'Guest´s Numbers';
		$data['phones'] = $this->phonebook->getAll($clientP);
		$this->load->view('header', $data);
		$this->load->view('guestsNumber', $data);
		$this->load->view('footer');
	}

	/**
	 * invoices
	 * Método que carga la vista facturas.
	 */
	public function invoices()
	{
		if($_SESSION['userType'] == STORE)  // Si el usuario es tienda.
		{
			$data['title'] = lang('invoices');
			$store = $_SESSION['userId'];
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$from = $this->input->post('from', TRUE);
				$to = $this->input->post('to', TRUE);
				$data['from'] = $from;
				$data['to'] = $to;
				$data['store'] = $store;
				$rs = $this->transaction->getInvoice(STORE, $from, $to, $store);
				$data['deposits'] = $rs->result();
				if($store != '' && ($from != '' || $to != ''))
				{
					$data['case'] = 'a3';  // Con tiendas y al menos una fecha.
				}
				else
				{
					$data['case'] = 'a2';  // Solo tiendas.
				}
			}
			else
			{
				$data['from'] = $data['to'] = '';
				$data['store'] = $store;
				$rs = $this->transaction->getInvoice(STORE, '', '', $store);
				$data['case'] = 'a2';
				$data['deposits'] = $rs->result();
			}

			$this->load->view('header', $data);
			$this->load->view('invoices', $data);
			$this->load->view('footer');
		}
		if($_SESSION['userType'] == ADMIN)  // Si el usuario es administrador.
		{
			$data['stores'] = $this->client->getStoresToInvoice();
			$data['title'] = 'Invoices';
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$from = $this->input->post('from', TRUE);
				$to = $this->input->post('to', TRUE);
				$store = $this->input->post('store', TRUE);
				$data['from'] = $from;
				$data['to'] = $to;
				$data['store'] = $store;
				$rs = $this->transaction->getInvoice(ADMIN, $from, $to, $store);
				$data['deposits']=$rs->result();
				if($store == '')
				{
					$data['case'] = 'a1';  // Admin sin filtos.
				}
				else
				{
					if($store != '' && ($from != '' || $to != ''))
					{
						$data['case'] = 'a3';  // Con tiendas y al menos una fecha.
					}
					else
					{
						$data['case'] = 'a2';  // Solo tiendas.
					}
				}
			}
			else
			{
				$data['from'] = $data['to'] = $data['store'] = '';

				$rs = $this->transaction->getInvoice(ADMIN, '', '', '');
				$data['deposits'] = $rs->result();
				$data['case'] = 'a1';  // Admin sin filtos.
			}

			$this->load->view('header', $data);
			$this->load->view('invoices', $data);
			$this->load->view('footer');
		}
	}

	/**
	 * pdf
	 * Método que genera el reporte en pdf.
	 */
	public function pdf($date, $store)
	{
		$this->load->library('mpdf');
		$rs = $this->transaction->getSpecialInvoice($date, $store);
		$data['items'] = $rs->result();
		$cdata = $this->client->getById($store, STORE);
		$cdata->postal_code = (empty($cdata->city) ? '' : $cdata->city).
			(', ' . (empty($cdata->state) ? '' : $cdata->state)).
			(', ' . (empty($cdata->country) ? '' : $cdata->country)).
			(', ' . (empty($cdata->zip) ? '' : $cdata->zip));
		$data['cdata'] = $cdata;
		$data['from'] = (new \DateTime($date))->format('d/m/Y');
		$data['invoice_date'] = (new \DateTime('now'))->format('d/m/y');
		//$data['userType'] = 'Admin';
		$mpdf = new mPDF('L',  // mode - default ''
						 'LETTER',    // format - A4, for example, default ''
						 0,     // font size - default 0
						 '',    // default font family
						 10,    // margin_left
						 10,    // margin right
						 16,    // margin top
						 16,    // margin bottom
						 9,     // margin header
						 9,     // margin footer
						 'L');
		ob_start();
		$mpdf->WriteHTML($this->load->view('pdf', $data, TRUE));
		ob_clean();
		$mpdf->Output();
	}

	/**
	 * salesByProduct.
	 * Método que permite generar el reporte de ventas por producto.
	 */
	public function salesByProduct()
	{
		if($_SESSION['userType'] == STORE)  // Si el usuario es tienda.
		{
			$data['title'] = lang('sales_by_product');
			$store = $_SESSION['userId'];
			$data['products'] = $this->product->getProductsToReport($store);
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$from = $this->input->post('from', TRUE);
				$to = $this->input->post('to', TRUE);
				$product = $this->input->post('product', TRUE);
				$data['from'] = $from;
				$data['to'] = $to;
				$data['store'] = $store;
				$data['product'] = $product;
				$rs = $this->transaction->getSales(STORE, $from, $to, $store ,$product);
				$data['deposits'] = $rs->result();
				if($product == '')
				{
					$data['case'] = 'a1';  // sin filtos.
				}
				else
				{
					if($product != '' && ($from != '' || $to != ''))
					{
						$data['case'] = 'a3';  // Con producto y al menos una fecha.
					}
					else
					{
						$data['case'] = 'a2';  // Solo producto.
					}
				}
			}
			else
			{
				$data['from'] = $data['to'] = $data['product'] = '';
				$data['store'] = $store;
				$rs = $this->transaction->getSales(STORE, '', '', $store, '');
				$data['case'] = 'a1';
				$data['deposits'] = $rs->result();
			}

			$this->load->view('header', $data);
			$this->load->view('salesByProduct', $data);
			$this->load->view('footer');
		}
	}

	/**
	 * salesBySeller.
	 * Método que permite generar el reporte de ventas por vendedor.
	 */
	public function salesBySeller()
	{
		$this->lang->load('report', $this->getLanguage());
		if($_SESSION['userType'] == ADMIN)  // Si el usuario es administrador.
		{
			$data['title'] = lang('sales_by_seller');
			$data['products'] = $this->product->getProductsToReport('');
			$data['sellers'] = $this->user->getAll(SELLER);
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$from = $this->input->post('from', TRUE);
				$to = $this->input->post('to', TRUE);
				$seller = $this->input->post('seller', TRUE);
				$product = $this->input->post('product', TRUE);
				$data['product'] = $product;
				$data['from'] = $from;
				$data['to'] = $to;
				$data['seller'] = $seller;
				$rs = $this->transaction->getSalesBySeller($from, $to, $seller, $product);
				$data['deposits'] = $rs->result();
				if($seller == '')
				{
					$data['case'] = 'a1';  // sin filtos.
				}
				else
				{
					if($seller != '' && ($from != '' || $to != ''))
					{
						$data['case'] = 'a3';  // Con producto y al menos una fecha.
					}
					else
					{
						$data['case'] = 'a2';  // Solo producto.
					}
				}
			}
			else
			{
				$data['from'] = $data['to'] = $data['seller'] = $data['product'] = '';
				$rs = $this->transaction->getSalesBySeller('', '', '', '');
				$data['case'] = 'a1';
				$data['deposits'] = $rs->result();
			}

			$this->load->view('header', $data);
			$this->load->view('salesBySeller', $data);
			$this->load->view('footer');
		}
		else
		{
			redirect(base_url('home'));
		}
	}

	/**
	 * sellerEarnings
	 * Reporte que muestra el listado de ganancias del vendedor, agrupadas por tienda o producto.
	 */
	public function sellerEarnings()
	{
		if($_SESSION['userType'] == ADMIN)  // Si el usuario es administrador.
		{
			$data['title'] = 'Agents earnings';
			$data['totalAmount'] = $data['totalEarnings'] = 0;
			$data['users'] = $this->user->getAll(SELLER);

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$from = $this->input->post('from', TRUE);
				$to = $this->input->post('to', TRUE);
				$userId = $this->input->post('userId', TRUE);
				$groupedBy = $this->input->post('groupedBy', TRUE);
				$data['from'] = $from;
				$data['to'] = $to;
				$data['userId'] = $userId;
				$data['groupedBy'] = $groupedBy;
				$data['earnings'] = $this->transaction->getEarnings($from, $to, $groupedBy, $userId);
				// Sumo los valores para el gran total.
				foreach($data['earnings'] as &$earning)
				{
					$data['totalAmount'] += $earning->total;
					$data['totalEarnings'] += $earning->userEarnings;
				}
			}
			else
			{
				// Por defecto muestro las fechas de este mes.
				$data['from'] = date('Y-m-01');
				$data['to'] = date('Y-m-d');
				$data['userId'] = '';
				$data['groupedBy'] = 'clientId';
				$data['earnings'] = array();
			}

			$this->load->view('header', $data);
			$this->load->view('sellerEarnings', $data);
			$this->load->view('footer');
		}
		else
		{
			redirect(base_url('home'));
		}
	}

	/**
	 * viewInvoice
	 */
	public function viewInvoice()
	{
		$data['stores'] = $this->client->getStoresToInvoice();
		$data['title'] = 'Invoices';
		$date = $this->uri->segment(3);
		$store = $this->uri->segment(4);
		$data['from'] = $date;
		$data['to'] = $date;
		$data['store'] = $store;
		$rs = $this->transaction->getSpecialInvoice($date, $store);
		$data['deposits'] = $rs->result();
		$data['case'] = 'inv';
		$data['exp'] = TRUE;
		$this->load->view('header', $data);
		$this->load->view('invoices', $data);
		$this->load->view('footer');
	}
}