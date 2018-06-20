<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <a href="<?= base_url('admin/providersList'); ?>">Providers</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="cProvider" id="cProvider" action="<?= base_url('admin/'.$labels['action']); ?>" method="post">
			<label for="id">ID: </label>
			<input type="text" id="id" name="id"<? if(isset($selProvider->id)) echo ' value="'.$selProvider->id.'" readonly'; ?> placeholder="Provider identification" required>
			<label for="name">Name: </label>
			<input type="text" id="name" name="name" value="<? if(isset($selProvider->name)) echo $selProvider->name; ?>" placeholder="Name" required>
			<label for="url">URL: </label>
			<input type="text" id="url" name="url" value="<? if(isset($selProvider->url)) echo $selProvider->url; ?>" placeholder="URL" required>
			<label for="username">Username: </label>
			<input type="text" id="username" name="username" value="<? if(isset($selProvider->username)) echo $selProvider->username; ?>" placeholder="Username" required>
			<label for="password">Password: </label>
			<input type="text" id="password" name="password" value="<? if(isset($selProvider->password)) echo $selProvider->password; ?>" placeholder="Password" required>
			<div class="buttonsDiv">
				<input type="submit" value="<?= $labels['btn']; ?>">
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#cProvider').validate({
			errorPlacement: function(error, element){
				// Append error within linked label
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});
	});
</script>
