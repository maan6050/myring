<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <a href="<?= base_url('admin/sellersList'); ?>">Agents</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="cUser" id="cUser" action="<?= base_url('admin/'.$labels['action']); ?>" method="post">
			<input type="hidden" name="id" value="<? if(isset($selUser->id)) echo $selUser->id; ?>">
			<label for="name">Name: </label>
			<input type="text" id="name" name="name" value="<? if(isset($selUser->name)) echo $selUser->name; ?>" placeholder="Name" required>
			<label for="email">Email: </label>
			<input type="email" id="email" name="email" value="<? if(isset($selUser->email)) echo $selUser->email; ?>" placeholder="Email" required>
			<label for="password"><?= $labels['pw']; ?> </label>
			<input type="text" id="password" name="password" <?= $labels['req']; ?> placeholder="Password">
			<div class="buttonsDiv">
				<input type="submit" value="<?= $labels['btn']; ?>">
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#cUser').validate({
			errorPlacement: function(error, element){
				// Append error within linked label
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});
	});
</script>
