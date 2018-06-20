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
			<a href="<?= base_url('admin/sellerCreateForm/'); ?>">New agent</a>
			<div class="clear"></div>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th><th>Email</th><th>Actions</th>
				</tr>
			</thead>
			<tbody><?
				foreach($users as $user)
				{ ?>
					<tr>
						<td><?= $user->name; ?></td><td><?= $user->email; ?></td>
						<td class="actionsTd">
							<a href="<?= base_url('admin/sellerEditForm/'.$user->id); ?>"><i class="fa fa-refresh" aria-hidden="true"></i>Update</a>
							<a href="<?= base_url('admin/sellerProductsForm/'.$user->id); ?>"><i class="fa fa-refresh" aria-hidden="true"></i>Fees</a>
							<a href="#" data-id="<?= $user->id; ?>" class="deleteItem"><i class="fa fa-times" aria-hidden="true"></i>Delete</a>
						</td>
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
			if(confirm('Are you sure you want to remove this agent?')){
				window.location = '<?= base_url('admin/sellerDelete/'); ?>' + $(this).attr('data-id');
			}
		});
	});
</script>
