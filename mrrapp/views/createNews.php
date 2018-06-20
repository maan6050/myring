<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <a href="<?= base_url('content/newsList'); ?>">News</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="cNews" id="cNews" action="<?= base_url('content/'.$labels['action']); ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="id" value="<? if(isset($selNews->id)) echo $selNews->id; ?>">
			<label for="created">Date: </label>
			<input type="date" id="created" name="created" value="<?= isset($selNews->created) ? $selNews->created : date('Y-m-d'); ?>" placeholder="Publishing date" required>
			<label for="title">Title: </label>
			<input type="text" id="title" name="title" value="<? if(isset($selNews->title)) echo $selNews->title; ?>" placeholder="News title" required>
			<label for="image"><?= $labels['image']; ?> <em>150px x 70px max.</em></label>
			<input type="file" id="image" name="image">
			<div class="clear10"></div>
			<div class="clear10"></div>
			<label for="content">Content: </label>
			<textarea id="content" name="content" rows="5"><? if(isset($selNews->content)) echo $selNews->content; ?></textarea>
			<div class="buttonsDiv">
				<input type="submit" value="<?= $labels['btn']; ?>">
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#cNews').validate({
			errorPlacement: function(error, element){
				// Append error within linked label
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});
	});
</script>
