<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <a href="<?= base_url('admin/countriesList'); ?>">Countries</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="cCountry" id="cCountry" action="<?= base_url('admin/countryEdit'); ?>" method="post">
			<input type="hidden" name="id" value="<? if(isset($selCountry->id)) echo $selCountry->id; ?>">
			<label>Name: </label>
			<strong><? if(isset($selCountry->name)) echo $selCountry->name; ?></strong>
			<div class="clear10"></div>
			<label>Dialcode: </label>
			<strong><? if(isset($selCountry->dialcode)) echo $selCountry->dialcode; ?></strong>
			<div class="clear10"></div>
			<label for="preferred">Preferred: </label>
			<select id="preferred" name="preferred">
				<option value="n">No</option>
				<option value="y"<? if(isset($selCountry->preferred) && $selCountry->preferred == 'y') echo ' selected'; ?>>Yes</option>
			</select>
			<label for="status">Status: </label>
			<select id="status" name="status">
				<option value="i">Inactive</option>
				<option value="a"<? if(isset($selCountry->status) && $selCountry->status == 'a') echo ' selected'; ?>>Active</option>
			</select>
			<div class="buttonsDiv">
				<input type="submit" value="Update">
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#cCountry').submit(function(event){
			if('<?= $selCountry->status; ?>' == 'a' && $('#status option:selected').val() == 'i'){
				// Estaba activo el país y ahora quiere inactivarlo.
				if(confirm('Are you sure you want to inactivate this country? All associated products will be deleted.')){
					return true;
				}else{
					event.preventDefault();  // No envío el formulario.
				}
			}
		});
	});
</script>
