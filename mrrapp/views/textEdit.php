<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>"><?= lang('home'); ?></a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="text" id="account" action="<?= base_url('admin/contents'); ?>" method="post">
			<div class="tabs-container">
				<ul class="tabs">
					<li class="tab-link <?= $tab == 1 ? 'current' : ''; ?>" data-tab="tab-1"><?= $NUMTitle; ?></li>
					<li class="tab-link <?= $tab == 2 ? 'current' : ''; ?>" data-tab="tab-2">Pinless World numbers</li>
					<li class="tab-link <?= $tab == 3 ? 'current' : ''; ?>" data-tab="tab-3"><?= $MRRTitle; ?></li>
					<li class="tab-link <?= $tab == 4 ? 'current' : ''; ?>" data-tab="tab-4">Pinless World rates</li>
					<li class="tab-link <?= $tab == 5 ? 'current' : ''; ?>" data-tab="tab-5"><?= $MXITitle; ?></li>
				</ul>
				<div id="tab-1" class="tab-content <?= $tab == 1 ? 'current' : ''; ?>">
					<textarea name="NUM-value"><?= $NUMValue; ?></textarea>
				</div>
				<div id="tab-2" class="tab-content <?= $tab == 2 ? 'current' : ''; ?>">
					<textarea name="NUC-value"><?= $NUCValue; ?></textarea>
				</div>
				<div id="tab-3" class="tab-content <?= $tab == 3 ? 'current' : ''; ?>">
					<textarea name="MRR-value"><?= $MRRValue; ?></textarea>
				</div>
				<div id="tab-4" class="tab-content <?= $tab == 4 ? 'current' : ''; ?>">
					<textarea name="MRC-value"><?= $MRCValue; ?></textarea>
				</div>
				<div id="tab-5" class="tab-content <?= $tab == 5 ? 'current' : ''; ?>">
					<textarea name="MXI-value"><?= $MXIValue; ?></textarea>
				</div>
			</div>
			<div class="clear10"></div>
			<div class="buttonsDiv">
				<input type="submit" value="Update">
			</div>
		</form>
	</div>
</div>
<script src="<?= base_url('js/modules/pinless_admin.js'); ?>"></script>
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=<?= TINYMCE_APIKEY; ?>"></script>
<script  type="text/javascript" charset="utf-8" async defer>
	tinymce.init({
		selector: 'textarea',
		height: 500,
		theme: 'modern',
		plugins: [
			'autolink lists link image hr anchor',
			'searchreplace visualblocks visualchars code fullscreen',
			'media nonbreaking table contextmenu directionality',
			'paste textcolor colorpicker textpattern imagetools codesample toc'
		],
		toolbar1: 'undo redo | insert | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
		image_advtab: true,
		content_css: [
			'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
			'//www.tinymce.com/css/codepen.min.css'
		]
	});
</script>
<script type="text/javascript"><?
	if(isset($msg))
	{ ?>
		alert('<?= $msg; ?>'); <?
	} ?>
</script>
