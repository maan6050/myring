<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <a href="<?= base_url('content/slidesList'); ?>">Slides</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="cSlide" id="cSlide" action="<?= base_url('content/slideCreate'); ?>" method="post" enctype="multipart/form-data">
			<label for="image">Image: <em>1100px x 400px.</em></label>
			<input type="file" id="image" name="image" required>
			<div class="clear10"></div>
			<div class="clear10"></div>
			<div class="buttonsDiv">
				<input type="submit" value="Create">
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#cSlide').validate({
			errorPlacement: function(error, element){
				// Append error within linked label
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});
	});
</script>
