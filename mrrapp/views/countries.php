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
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Name</th><th>Preferred</th><th>Status</th><th>Actions</th>
				</tr>
			</thead>
			<tbody><?
				foreach($countries as $country)
				{ ?>
					<tr>
						<td><?= $country->name.' - <em>'.$country->dialcode.'</em>'; ?></td>
						<td><?= $country->preferred; ?></td><td><?= $country->status; ?></td>
						<td class="actionsTd">
							<a href="<?= base_url('admin/countryEditForm/'.$country->id); ?>"><i class="fa fa-refresh" aria-hidden="true"></i>Update</a><?
							if($country->status == 'Active')
							{ ?>
								<a href="<?= base_url('admin/productsList/'.$country->id); ?>"><i class="fa fa-credit-card" aria-hidden="true"></i>Products</a><?
							} ?>
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
</script>
