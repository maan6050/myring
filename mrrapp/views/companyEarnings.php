<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<div class="searchDiv">
			<form name="search" id="search" action="<?= base_url('report/companyEarnings'); ?>" method="post">
				<input type="date" id="from" name="from" value="<?= $from; ?>" placeholder="">
				<input type="date" id="to" name="to" value="<?= $to; ?>" placeholder="">
				<select id="groupedBy" name="groupedBy">
					<option value="clientId">Grouped by store</option>
					<option value="productId"<? if($groupedBy == 'productId') echo ' selected'; ?>>Grouped by product</option>
				</select>
				<input type="submit" value="Search">
			</form>
		</div>
		<div class="fcHeader borderBottom subTitle">
			<h3>Grouped by <?= $groupedBy == 'productId' ? 'product' : 'store'; ?></h3>
			<div class="clear"></div>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th>
					<th>Real Top-up</th>
					<th>Included Charge</th>
					<th>Company Gross Profit</th>
					<th>Company Net Profit</th>
				</tr>
			</thead>
			<tbody><?
				if(count($earnings) > 0)
				{
					foreach($earnings as $e)
					{ ?>
						<tr>
							<td><?= $e->name; ?></td>
							<td class="alignCenter"><?= $e->total; ?></td>
							<td class="alignCenter"><?= $e->included; ?></td>
							<td class="alignCenter"><?= number_format($e->earnings + $e->included, 2, '.', ','); ?></td>
							<td class="alignCenter"><span class="<?= $e->class; ?>"><?= number_format($e->netProfit, 2, '.', ','); ?></span></td>
						</tr><?
					} ?>
					<tr>
						<th><?= count($earnings); ?> rows</th>
						<th><?= number_format($totalAmount, 2, '.', ','); ?></th>
						<th><?= number_format($totalIncluded, 2, '.', ','); ?></th>
						<th><?= number_format($totalEarnings, 2, '.', ','); ?></th>
						<th><?= number_format($totalNetProfit, 2, '.', ','); ?></th>
					</tr><?
				}
				else
				{ ?>
					<td colspan="4">There are no transaction records for these dates.</td><?
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

