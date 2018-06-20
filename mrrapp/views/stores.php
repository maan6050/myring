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
			<a href="<?= base_url($controller.'/storeCreateForm'); ?>">New store</a>
			<div class="clear"></div>
		</div>
		<div class="searchDiv">
			<form name="search" id="search" action="<?= base_url($controller.'/storesList'); ?>" method="post">
				<input type="text" id="name" name="name" value="<? if(isset($name)) echo $name; ?>" placeholder="Search by name">
				<input type="submit" value="Search">
			</form>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th><th>Agent in charge</th><th>Max. balance</th><th>Available</th><th><a href="<?= base_url($controller.'/storesList/balance'); ?>">Due</a></th><th>Payment method</th><th>Actions</th>
				</tr>
			</thead>
			<tbody><?
				if(count($clients) > 0)
				{
					foreach($clients as $client)
					{ ?>
						<tr>
							<td><?= $client->name; ?></td>
							<td><?= $client->userName; ?></td>
							<td align="right"><?= $client->maxBalance; ?></td>
							<td align="right"><?= $client->available; ?></td>
							<td align="right"><span class="<?= $client->class; ?>"><?= $client->balance; ?></span></td>
							<td><?= $client->paymentMethodName; ?></td>
							<td class="actionsTd">
								<a href="<?= base_url($controller.'/storeEditForm/'.$client->id); ?>"><i class="fa fa-refresh" aria-hidden="true"></i>Update</a><?
								if($_SESSION['userType'] == ADMIN)
								{ ?>
									<a href="<?= base_url('admin/storeProductsForm/'.$client->id); ?>"><i class="fa fa-refresh" aria-hidden="true"></i>Fees</a>
									<a href="#" data-id="<?= $client->id; ?>" class="deleteItem"><i class="fa fa-times" aria-hidden="true"></i>Delete</a><?
								} ?>
							</td>
						</tr><?
					}
				}
				else
				{ ?>
					<tr>
						<td align="center" colspan="6">No clients found.</td>
					</tr><?
				} ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript"><?
	if(isset($msg))
	{ ?>
		alert('<?= $msg; ?>'); <?
	} ?>

	jQuery(document).ready(function($){
		$(document).on('click', '.deleteItem', function() {
			if(confirm('Are you sure you want to remove this store?')){
				window.location = '<?= base_url($controller.'/storeDelete/'); ?>' + $(this).attr('data-id');
			}
		});
	});
</script>