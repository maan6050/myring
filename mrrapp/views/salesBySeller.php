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
		<div class="searchDiv" style="display:inline-block;">
			<form name="search" id="search" action="<?= base_url('report/salesBySeller'); ?>" method="post">
				<input type="date" id="from" name="from" value="<?= $from; ?>" placeholder="">
				<input type="date" id="to" name="to" value="<?= $to; ?>" placeholder=""><?
				if($_SESSION['userType'] == ADMIN)
				{ ?>
					<select id="seller" name="seller"><?
					if(count($sellers) > 0)
					{ ?>
						<option value="">All agents</option><?
						foreach($sellers as $value)
						{ ?>
							<option id="store" value="<?= $value->id; ?>"<? if($seller == $value->id) echo ' selected'; ?>><?= $value->name; ?></option><?
						}
					}
					else
					{ ?>
						<option value=""><?= lang('option_all'); ?></option><?
					} ?>
					</select>
					<select id="product" name="product"><?
						if(count($products) > 0)
						{ ?>
							<option value="">All products</option><?
							foreach($products as $value)
							{ ?>
								<option id="op_product" value="<?= $value->id; ?>"<? if($product == $value->id) echo ' selected'; ?>>
									<?= $value->name.' - '.$value->countryId; ?>
								</option><?
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
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead><?
				switch($case)
				{
					case 'a1': ?>
						<tr>
							<th><?= lang('col_seller'); ?></th>
							<th><?= lang('col_transactions'); ?></th>
							<th><?= lang('col_amount'); ?></th>
							<th><?= lang('col_service_charge'); ?></th>
							<th><?= lang('col_include_charge'); ?></th>
							<th>Total</th>
						</tr><?
						break;
					case 'a2': ?>
						<tr>
							<th><?= lang('col_date'); ?></th>
							<th><?= lang('col_seller'); ?></th>
							<th><?= lang('col_product'); ?></th>
							<th><?= lang('col_phone'); ?></th>
							<th><?= lang('col_amount'); ?></th>
							<th><?= lang('col_service_charge'); ?></th>
							<th><?= lang('col_include_charge'); ?></th>
							<th>Total</th>
						</tr><?
						break;
					case 'a3': ?>
						<tr>
							<th><?= lang('col_date'); ?></th>
							<th><?= lang('col_transId'); ?></th>
							<th><?= lang('col_seller'); ?></th>
							<th><?= lang('col_product'); ?></th>
							<th><?= lang('col_phone'); ?></th>
							<th>Pin</th>
							<th><?= lang('col_status'); ?></th>
							<th><?= lang('col_amount'); ?></th>
							<th><?= lang('col_service_charge'); ?></th>
							<th><?= lang('col_include_charge'); ?></th>
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
							$numTrans = $totalServChar = $totalInclChar = $totalAmount = $total = 0;
							foreach($deposits as $depo)
							{ ?>
								<tr class="alignRight">
									<td align="left"><?= $depo->seller; ?></td>
									<td><?= $depo->numTrans; ?></td>
									<td><?= number_format($depo->amount, 2, '.', ','); ?></td>
									<td><?= $depo->serviceCharge; ?></td>
									<td><?= $depo->includeCharge; ?></td>
									<td><?= number_format($depo->total, 2, '.', ','); ?></td>
								</tr><?
								$numTrans += $depo->numTrans;
								$totalServChar += $depo->serviceCharge;
								$totalInclChar += $depo->includeCharge;
								$totalAmount += $depo->amount;
								$total += $depo->total;
							} ?>
							<tr class="alignRight" style="font-weight:bold;">
								<td align="left"><?= lang('totals'); ?></td>
								<td><?= number_format($numTrans, 0, '', ','); ?></td>
								<td><?= number_format($totalAmount, 2, '.', ','); ?></td>
								<td><?= number_format($totalServChar, 2, '.', ','); ?></td>
								<td><?= number_format($totalInclChar, 2, '.', ','); ?></td>
								<td><?= number_format($total, 2, '.', ','); ?></td>
							</tr><?
						break;
					case 'a2':
						$totalServChar = $totalInclChar = $totalAmount = $total = 0;
						foreach($deposits as $depo)
						{ ?>
							<tr class="alignRight">
								<td align="left"><?= $depo->created; ?></td>
								<td align="left"><?= $depo->seller; ?></td>
								<td align="left"><?= $depo->product.' - '.$depo->countryId; ?></td>
								<td><?= $depo->phone; ?></td>
								<td><?= $depo->amount; ?></td>
								<td><?= $depo->serviceCharge; ?></td>
								<td><?= $depo->includeCharge; ?></td>
								<td><?= $depo->total; ?></td>
							</tr><?
							$totalServChar += $depo->serviceCharge;
							$totalInclChar += $depo->includeCharge;
							$totalAmount += $depo->amount;
							$total += $depo->total;
						} ?>
						<tr style="font-weight:bold;">
							<td colspan="4" style="text-align:right;"><?= lang('totals'); ?></td>
							<td><?= number_format($totalAmount, 2, '.', ','); ?></td>
							<td><?= number_format($totalServChar, 2, '.', ','); ?></td>
							<td><?= number_format($totalInclChar, 2, '.', ','); ?></td>
							<td><?= number_format($total, 2, '.', ','); ?></td>
						</tr><?
						break;
					case 'a3':
						$totalServChar = $totalInclChar = $totalAmount = $total = 0;
						foreach($deposits as $depo)
						{ ?>
							<tr class="alignRight">
								<td align="left"><?= $depo->created; ?></td>
								<td><?= $depo->transId; ?></td>
								<td align="left"><?= $depo->seller; ?></td>
								<td align="left"><?= $depo->product.' - '.$depo->countryId; ?></td>
								<td><?= $depo->phone; ?></td>
								<td><?= $depo->pin; ?></td>
								<td><?= $depo->status; ?></td>
								<td><?= $depo->amount; ?></td>
								<td><?= $depo->serviceCharge; ?></td>
								<td><?= $depo->includeCharge; ?></td>
								<td><?= $depo->total; ?></td>
							</tr><?
							$totalServChar += $depo->serviceCharge;
							$totalInclChar += $depo->includeCharge;
							$totalAmount += $depo->amount;
							$total += $depo->total;
						} ?>
						<tr style="font-weight:bold;">
							<td colspan="7" style="text-align:right;"><?= lang('totals'); ?></td>
							<td><?= number_format($totalAmount, 2, '.', ','); ?></td>
							<td><?= number_format($totalServChar, 2, '.', ','); ?></td>
							<td><?= number_format($totalInclChar, 2, '.', ','); ?></td>
							<td><?= number_format($total, 2, '.', ','); ?></td>
						</tr><?
						break;
					}
				}
				else
				{
					echo '<td colspan="12">'.lang('no_transactions_found').'</td>';
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
