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
			<h3><?= $title .' - '.$_SESSION['userName']; ?></h3>
			<div class="clear"></div>
		</div>
		<div class="searchDiv">
			<form name="search" id="search" action="<?= base_url('report/recentDeposit'); ?>" method="post">
				<input type="date" id="from" name="from" value="<?= $from; ?>" placeholder="">
				<input type="date" id="to" name="to" value="<?= $to; ?>" placeholder="">
				<input type="submit" value="<?= lang('search'); ?>">
			</form>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?= lang('col_date'); ?></th>
					<th><?= lang('col_client'); ?>t</th>
					<th><?= lang('col_user'); ?></th>
					<th><?= lang('col_payment_method'); ?></th>
					<th><?= lang('col_reference'); ?></th>
					<th><?= lang('col_amount'); ?></th>
					<th><?= lang('col_comments'); ?></th>
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

	jQuery(document).ready(function($){
		$(document).on('click', '.deleteItem', function(){
			if(confirm('Are you sure you want to remove this number?')){
				window.location = '<?= base_url('admin/guestNumberDelete/'); ?>' + $(this).attr('data-id');
			}
		});
		$(document).on('click', '.deleteAll', function(){
			if(confirm('Are you sure you want to remove all numbers?')){
				window.location = '<?= base_url('admin/guestsNumbersDeleteAll'); ?>';
			}
		});
	});
</script>

