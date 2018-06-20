<?php
function storesBalance($clients)
{
	foreach($clients as &$c)
	{
		if($c->balance <= $c->maxBalance * 0.1)
		{
			// Sí ha gastado hasta el 10% del límite, se muestra verde.
			$c->class = 'green';
		}
		elseif($c->balance >= $c->maxBalance * 0.9)
		{
			// Sí ha gastado más del 90% del límite, se muestra rojo.
			$c->class = 'red';
		}
		else
		{
			$c->class = '';
		}
		$c->available = number_format($c->maxBalance - $c->balance, 2, '.', ',');
		$c->maxBalance = number_format($c->maxBalance, 0, '', ',');
		$c->balance = number_format($c->balance, 2, '.', ',');
	}
	return $clients;
}

function catchAlert($context, &$data, $callback)
{
	try
	{
		$callback($context, $data);
		$data['alert_type'] = 'success';
	}
	catch (Exception $exc)
	{
		$data['alert_text'] = $exc->getMessage();
		$data['alert_type'] = 'danger';
	}
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = \DateTime::createFromFormat($format, $date);
    return ($d && $d->format($format) == $date);
}

function ajaxResponse($context, $callback)
{
	$output = array();
	try
	{
		$output = $callback($context, $output);
		if (!isset($output['status'])) {
			$output['status'] = 'OK';
		}
	}
	catch(Exception $exc)
	{
		$output['status'] = $exc->getMessage();
	}
	echo json_encode($output);
	exit();
}

function customDebug($data, $use_die = TRUE)
{
	$except = new Exception();
	$trace = $except->getTrace();
	$info = $trace[1];
	if(isset($info['class']) && isset($info['function']))
	{
		echo "<br/>------------------<br/>";
		echo "Called from Class: {$info['class']} - Function: {$info['function']}";
	}
	echo '<pre>'.print_r($data, true).'</pre>';
	if($use_die)
	{
		die();
	}
}

function getArrayValue($obj, $name, $default = FALSE)
{
	$value = recursiveGetArrayValue($obj, $name);
	return ($value !== '@NF@') ? $value : $default;
}

function getBalanceStore()
{
	if($_SESSION['userType'] !== STORE)
	{
		return (object)array(
			'show' => FALSE
		);
	}

	$CI = &get_instance();
	$CI->load->model('client');

	$result = $CI->client->getById($_SESSION['userId'], STORE);

	$available = $balance = 0;

	if($result)
	{
		$available = ($result->maxBalance - $result->balance);
		$balance = (float)$result->balance;
	}
	return (object)array(
		'available' => $available,
		'balance' => $balance,
		'show' => TRUE
	);
}

function moneyFormat($val, $symbol = '$', $r = 2)
{
	$n = $val;
	$c = is_float($n) ? 1 : number_format($n,$r);
	$d = '.';
	$t = ',';
	$sign = ($n < 0) ? '-' : '';
	$i = $n=number_format(abs($n),$r);
	$j = (($j = strlen($i)) > 3) ? $j % 3 : 0;

	return  $symbol.$sign .($j ? substr($i,0, $j) + $t : '').preg_replace('/(\d{3})(?=\d)/',"$1" + $t,substr($i,$j)) ;
}

function recursiveGetArrayValue($array, $search, $separator = '/')
{
	$return = '@NF@';
	$attrs = explode($separator, $search);
	recursiveGetValue($array, $attrs, $return);
	return $return;
}

function recursiveGetValue(&$array, $attrs, &$return)
{
	if($return === '@NF@')
	{
		if(($attr = array_shift($attrs)) !== NULL)
		{
			if(isset($array["{$attr}"]))
			{
				$size = count($attrs);
				if($size)
				{
					if($size == 1 && (is_array($array["{$attr}"]) && is_numeric($attrs[0])))
					{
						$pos = (int)$attrs[0];
						$return = isset($array["{$attr}"][$pos]) ? $array["{$attr}"][$pos] : '@NF@';
					}
					else
					{
						recursiveGetValue($array["{$attr}"], $attrs, $return);
					}
				}
				else
				{
					$return = $array["{$attr}"];
				}
			}
			else
			{
				$return = '@NF@';
			}
		}
	}
}
?>