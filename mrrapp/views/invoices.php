<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>"><?= lang('home'); ?></a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<div class="searchDiv" style="display: inline-block;">
			<form name="search" id="search" action="<?= base_url('report/invoices'); ?>" method="post">
				<input type="date" id="from" name="from" value="<?= $from; ?>" placeholder="">
				<input type="date" id="to" name="to" value="<?= $to; ?>" placeholder=""><?
				if($_SESSION['userType'] == ADMIN)
				{ ?>
					<select id="store" name="store"><?
					if(count($stores) > 0)
					{ ?>
						<option value=""><?= lang('option_all'); ?></option><?
						foreach($stores as $value)
						{ ?>
							<option  id="store" value="<?= $value->id; ?>"<? if($store == $value->id) echo ' selected'; ?>><?= $value->name; ?></option><?
						}
					}
					else
					{ ?>
						<option value=""><?= lang('option_all'); ?></option><?
					} ?>
				</select><?
				} ?>
				<input type="submit" value="<?= lang('bttn_search'); ?>">
			</form>
		</div>
		<div class="searchDiv" style="display:inline-block; float:right;">
			<form action="<?= base_url('report/generateInvoice'); ?>" method="get" accept-charset="utf-8"><?
			if(isset($exp))
			{ ?>
				<input type="hidden" id="date" name="date" value="<?= $from; ?>">
				<input type="hidden" name="sto" value="<?= $store; ?>">
				<input type="submit" name="excel" value="Excel">
				<input type="submit" name="pdf" value="PDF"><?
			} ?>
			</form>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead><?
				switch($case)
				{
					case 'a1': ?>
						<tr>
							<th><?= lang('col_store'); ?></th>
							<th><?= lang('col_amount'); ?></th>
							<th><?= lang('col_service_charge'); ?></th>
							<th><?= lang('col_include_charge'); ?></th>
							<th><?= lang('col_profit'); ?></th>
							<th>Total</th>
						</tr><?
						break;
					case 'a2': ?>
						<tr>
							<th><?= lang('col_date'); ?></th>
							<th><?= lang('col_store'); ?></th>
							<th><?= lang('col_product'); ?></th>
							<th><?= lang('col_client_phone'); ?></th>
							<th><?= lang('col_phone'); ?></th>
							<th><?= lang('col_amount'); ?></th>
							<th><?= lang('col_service_charge'); ?></th>
							<th><?= lang('col_include_charge'); ?></th>
							<th><?= lang('col_profit'); ?></th>
							<th>Total</th>
						</tr><?
						break;
					case 'a3': ?>
						<tr>
							<th><?= lang('col_date'); ?></th>
							<th><?= lang('col_transactions'); ?></th>
							<th><?= lang('col_store'); ?></th>
							<th><?= lang('col_amount'); ?></th>
							<th><?= lang('col_profit'); ?></th>
							<th>Total</th>
							<th><?= lang('options'); ?></th>
						</tr><?
						break;
					default: ?>
						<tr>
							<th><?= lang('col_date'); ?></th>
							<th><?= lang('col_transId'); ?></th>
							<th><?= lang('col_store'); ?></th>
							<th><?= lang('col_product'); ?></th>
							<th><?= lang('col_client_phone'); ?></th>
							<th><?= lang('col_phone'); ?></th>
							<th>Pin</th>
							<th><?= lang('col_status'); ?></th>
							<th><?= lang('col_amount'); ?></th>
							<th><?= lang('col_service_charge'); ?></th>
							<th><?= lang('col_include_charge'); ?></th>
							<th><?= lang('col_profit'); ?></th>
							<th>Total</th>
						</tr><?
						break;
				} ?>
			</thead>
			<tbody><?
				if(count($deposits) > 0)
				{
					switch($case)
					{
					case 'a1':
						$totalServChar = $totalInclChar = $totalAmount = $totalProfit = $total = 0;
						foreach($deposits as $depo)
						{ ?>
							<tr>
								<td><?= $depo->store; ?></td>
								<td><?= $depo->amount; ?></td>
								<td><?= $depo->serviceCharge; ?></td>
								<td><?= $depo->includeCharge; ?></td>
								<td><?= $depo->profit; ?></td>
								<td><?= $depo->total; ?></td>
							</tr><?
							$totalServChar += $depo->serviceCharge;
							$totalInclChar += $depo->includeCharge;
							$totalAmount += $depo->amount;
							$totalProfit += $depo->profit;
							$total += $depo->total;
						} ?>
						<tr style="font-weight: bold;">
							<td><?= lang('totals'); ?></td>
							<td><?= $totalAmount; ?></td>
							<td><?= $totalServChar; ?></td>
							<td><?= $totalInclChar; ?></td>
							<td><?= $totalProfit; ?></td>
							<td><?= $total; ?></td>
						</tr><?
						break;
					case 'a2':
						$totalServChar = $totalInclChar = $totalAmount = $totalProfit = $total = 0;
						foreach($deposits as $depo)
						{ ?>
							<tr>
								<td><?= $depo->created; ?></td>
								<td><?= $depo->store; ?></td>
								<td><?= $depo->product; ?></td>
								<td><?= $depo->clientPhone; ?></td>
								<td><?= $depo->phone; ?></td>
								<td><?= $depo->amount; ?></td>
								<td><?= $depo->serviceCharge; ?></td>
								<td><?= $depo->includeCharge; ?></td>
								<td><?= $depo->profit; ?></td>
								<td><?= $depo->total; ?></td>
							</tr><?
							$totalServChar += $depo->serviceCharge;
							$totalInclChar += $depo->includeCharge;
							$totalAmount += $depo->amount;
							$totalProfit += $depo->profit;
							$total += $depo->total;
						} ?>
						<tr style="font-weight: bold;">
							<td colspan="5" style="text-align: right"><?= lang('totals'); ?></td>
							<td><?= $totalAmount; ?></td>
							<td><?= $totalServChar; ?></td>
							<td><?= $totalInclChar; ?></td>
							<td><?= $totalProfit; ?></td>
							<td><?= $total; ?></td>
						</tr><?
						break;
					case 'a3':
						$totalTrans = $totalAmount = $totalProfit = $total = 0;
						foreach($deposits as $depo)
						{ ?>
							<tr>
								<td><?= $depo->date; ?></td>
								<td><?= $depo->numTrans?></td>
								<td><?= $depo->store; ?></td>
								<td><?= $depo->amount; ?></td>
								<td><?= $depo->profit; ?></td>
								<td><?= $depo->total; ?></td>
								<th><a href="<?= base_url('report/viewInvoice/'.$depo->date.'/'.$depo->clientId); ?>"><?= lang('option_view'); ?></a></th>
							</tr><?
							$totalTrans += $depo->numTrans;
							$totalAmount += $depo->amount;
							$totalProfit += $depo->profit;
							$total += $depo->total;
						} ?>
						<tr style="font-weight: bold;">
							<td><?= lang('totals'); ?></td>
							<td><?= $totalTrans; ?></td><td></td>
							<td><?= $totalAmount; ?></td>
							<td><?= $totalProfit; ?></td>
							<td><?= $total; ?></td>
						</tr><?
						break;
					default:
						$totalServChar = $totalInclChar = $totalAmount = $totalProfit = $total = 0;
						foreach($deposits as $depo)
						{ ?>
							<tr>
								<td><?= $depo->created; ?></td>
								<td><?= $depo->transId; ?></td>
								<td><?= $depo->store; ?></td>
								<td><?= $depo->product; ?></td>
								<td><?= $depo->clientPhone; ?></td>
								<td><?= $depo->phone; ?></td>
								<td><?= $depo->pin; ?></td>
								<td><?= $depo->status; ?></td>
								<td><?= $depo->amount; ?></td>
								<td><?= $depo->serviceCharge; ?></td>
								<td><?= $depo->includeCharge; ?></td>
								<td><?= $depo->profit; ?></td>
								<td><?= $depo->total; ?></td>
							</tr><?
							$totalServChar += $depo->serviceCharge;
							$totalInclChar += $depo->includeCharge;
							$totalAmount += $depo->amount;
							$totalProfit += $depo->profit;
							$total += $depo->total;
						} ?>
						<tr style="font-weight: bold;">
							<td colspan="8" style="text-align:right;"><?= lang('totals'); ?></td>
							<td><?= $totalAmount; ?></td>
							<td><?= $totalServChar; ?></td>
							<td><?= $totalInclChar; ?></td>
							<td><?= $totalProfit; ?></td>
							<td><?= $total; ?></td>
						</tr><?
					break;
					}
				}
				else
				{
					echo '<td colspan="7">'.lang('no_transactions_found').'</td>';
				} ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript"><?
	if(isset($msg))
	{ ?>
		alert('<?= $msg; ?>');<?
	} ?>
</script>

