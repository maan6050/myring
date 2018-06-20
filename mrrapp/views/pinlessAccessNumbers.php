<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>
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
		<div class="tabs-container">
			<ul class="tabs">
				<li class="tab-link <?= $tab == 1 ? 'current' : ''; ?>" data-tab="tab-1"><?= $NUMTitle; ?></li>
				<li class="tab-link <?= $tab == 2 ? 'current' : ''; ?>" data-tab="tab-2"><?= $NUCTitle; ?></li>
				<li class="tab-link <?= $tab == 3 ? 'current' : ''; ?>" data-tab="tab-3"><?= $MXITitle; ?></li>
			</ul>
			<div id="tab-1" class="tab-content <?= $tab == 1 ? 'current' : ''; ?>" style="overflow:auto; height:600px;">
				<?= $NUMValue; ?>
			</div>
			<div id="tab-2" class="tab-content <?= $tab == 2 ? 'current' : ''; ?>" style="overflow:auto; height:600px;">
				<?= $NUCValue; ?>
			</div>
			<div id="tab-3" class="tab-content <?= $tab == 3 ? 'current' : ''; ?>" style="overflow:auto; height:600px;">
				<?= $MXIValue; ?>
			</div>
		</div>
		<div class="clear10"></div>
	</div>
</div>
<script src="<?= base_url('js/modules/pinless_admin.js'); ?>"></script>