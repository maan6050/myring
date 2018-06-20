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
			<a href="<?= base_url('admin/providerCreateForm/'); ?>">New provider</a>
			<div class="clear"></div>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th><th>URL</th><th>Username</th><th>Actions</th>
				</tr>
			</thead>
			<tbody><?
				foreach($providers as $provider)
				{ ?>
					<tr>
						<td><?= $provider->name; ?></td><td><?= $provider->url; ?></td><td><?= $provider->username; ?></td>
						<td class="actionsTd">
							<a href="<?= base_url('admin/providerEditForm/'.$provider->id); ?>"><i class="fa fa-refresh" aria-hidden="true"></i>Update</a>
							<a href="#" data-id="<?= $provider->id; ?>" class="deleteItem"><i class="fa fa-times" aria-hidden="true"></i>Delete</a>
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
			if(confirm('Are you sure you want to remove this provider? All associated products will be deleted and this action can make the system unstable.')){
				window.location = '<?= base_url('admin/providerDelete/'); ?>' + $(this).attr('data-id');
			}
		});
	});
</script>
