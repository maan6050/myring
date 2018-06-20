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
			<form name="search" id="search" action="<?= base_url('report/sellerEarnings'); ?>" method="post">
				<input type="date" id="from" name="from" value="<?= $from; ?>" placeholder="">
				<input type="date" id="to" name="to" value="<?= $to; ?>" placeholder="">
				<select id="userId" name="userId"><?
					foreach($users as $user)
					{ ?>
						<option value="<?= $user->id; ?>"<? if($userId == $user->id) echo ' selected'; ?>><?= $user->name; ?></option><?
					} ?>
				</select>
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
					<th>Amount</th>
					<th>Earnings</th>
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
							<td class="alignCenter"><?= $e->userEarnings; ?></td>
						</tr><?
					} ?>
					<tr>
						<th><?= count($earnings); ?> rows</th>
						<th><?= number_format($totalAmount, 2, '.', ','); ?></th>
						<th><?= number_format($totalEarnings, 2, '.', ','); ?></th>
					</tr><?
				}
				else
				{ ?>
					<td colspan="3">There are no transaction records for these dates.</td><?
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
