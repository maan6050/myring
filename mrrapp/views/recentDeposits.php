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
			<form name="search" id="search" action="<?= base_url('report/recentDeposit'); ?>" method="post">
				<input type="date" id="from" name="from" value="<?= $from; ?>" placeholder="">
				<input type="date" id="to" name="to" value="<?= $to; ?>" placeholder="">
				<select id="store" name="store"><?
					if(count($stores) > 0)
					{ ?>
						<option value="">All</option><?
						foreach($stores as $value)
						{ ?>
							<option value="<?= $value->id; ?>"<? if($store == $value->id) echo ' selected'; ?>><?= $value->name; ?></option><?
						}
					}
					else
					{ ?>
						<option value="">All</option><?
					} ?>
				</select>
				<input type="submit" value="Search">
			</form>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Date</th>
					<th>Client</th>
					<th>User</th>
					<th>Payment Method</th>
					<th>Reference</th>
					<th>Amount</th>
					<th>Comments</th>
				</tr>
			</thead>
			<tbody><?
				if(count($deposits) > 0)
				{
					foreach($deposits as $depo)
					{ ?>
					<tr>
						<td><?= $depo->Date; ?></td>
						<td><?= $depo->Client; ?></td>
						<td><?= $depo->User; ?></td>
						<td><?
							switch($depo->paymentMethod)
							{
								case 'a':
									echo 'Ach Portal';
									break;
								case 'd':
									echo 'Direct distributor';
									break;
								case 'd':
									echo 'Direct sub-distributor';
									break;
								 default:
									echo 'Credit car portal';
									break;
							} ?>
						</td>
						<td><?= $depo->reference; ?></td>
						<td><?= $depo->amount; ?></td>
						<td><?= $depo->comments; ?></td>
					</tr><?
					}
				}
				else
				{
					echo '<td colspan="7">There are no deposit records for these dates.</td>';
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

