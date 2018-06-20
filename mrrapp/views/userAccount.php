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
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="account" id="account" action="<?= base_url(uri_string()); ?>" method="post">
			<label for="name">Name: </label>
			<input type="text" id="name" name="name" value="<? if(isset($selected->name)) echo $selected->name; ?>" placeholder="Name" required>
			<label for="email">Email: </label>
			<input type="email" id="email" name="email" value="<? if(isset($selected->email)) echo $selected->email; ?>" placeholder="Email" required>
			<div class="fcHeader borderBottom subTitle">
				<h3>Update password - <em>optional</em></h3>
				<div class="clear"></div>
			</div>
			<label for="password">New password: </label>
			<input type="password" id="password" name="password" placeholder="New password">
			<label for="password">Confirm password: </label>
			<input type="password" id="passwordConf" name="passwordConf" placeholder="Confirm password">
			<div class="buttonsDiv">
				<input type="submit" value="Update">
			</div>
		</form>
	</div>
</div>
<script type="text/javascript"><?
	if(isset($msg))
	{ ?>
		alert('<?= $msg; ?>'); <?
	} ?>

	jQuery(document).ready(function($){
		$('#account').validate({
			errorPlacement: function(error, element){
				// Append error within linked label
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});
	});
</script>
