<?php
$section_title = lang('section_title');
$detail_text = FALSE;
if($account->Ani && $code && $account->Balance)
{
	$detail_text = sprintf(lang('pinless_account_detail_text'), $account->Ani, $code, $account->Balance);
} ?>
<div class="hide" id="language-text">
	<span class="are_you_sure" data-value="<?= lang('are_you_sure'); ?>"></span>
	<span class="cancel_btn" data-value="<?= lang('cancel_btn'); ?>"></span>
	<span class="continue_btn" data-value="<?= lang('continue_btn'); ?>"></span>
</div>
<div class="centeredContent adminPage" id="pinless-admin">
	<h1><?= $section_title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>"><?= lang('breadcrumb_home'); ?></a> / <?= $section_title;; ?>
	</div>
	<div class="clear"></div><?
	if(isset($msg_code) && $msg_code)
	{ ?>
		<div class="alert alert-dismissible alert-<?= $msg_type; ?>" role="alert">
			<?= $msg_code; ?>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
		</div><?
	}
	if($detail_text)
	{ ?>
		<h3><?= $detail_text; ?></h3>
		<br /><?
	} ?>

	<div class="formContainer">
		<div class="clear"></div>
		<br /><?
		if($responseCode < 0)
		{ ?>
			<div class="alert alert-danger" role="alert">
				<strong><?= lang('error'); ?></strong>&nbsp;<?= lang('pinless_account_not_found'); ?> <?= anchor('pinlessAdminCtrl', lang('try_again')); ?>
			</div><?
		}
		else
		{ ?>
			<div class="alignRight">
				<?= anchor('pinlessAdminCtrl', lang('return_to')); ?>
			</div>
			<div class="tabs-container">
				<ul class="tabs">
					<li class="tab-link <?= (1 == $tab) ? 'current' : ''; ?>" data-tab="tab-1"><?= lang('tab_1'); ?></li>
					<li class="tab-link <?= (2 == $tab) ? 'current' : ''; ?>" data-tab="tab-2"><?= lang('tab_2'); ?></li>
					<li class="tab-link <?= (3 == $tab) ? 'current' : ''; ?>" data-tab="tab-3"><?= lang('tab_3'); ?></li>
				</ul>
				<div id="tab-1" class="tab-content <?= (1 == $tab) ? 'current' : ''; ?>"><?
					$body = simplexml_load_string($aniList->any);
					$this->load->view('/pinlessAdmin/_associated_phones', array(
						'ani' => $account->Ani,
						'code' => $code,
						'registered_numbers' => $body)
					); ?>
				</div>
				<div id="tab-2" class="tab-content <?= (2 == $tab) ? 'current' : ''; ?>"><?
					$this->load->view('/pinlessAdmin/_speed_dials', array(
						'ani' => $account->Ani,
						'code' => '',
						'speed_dials' => ($speedDialList->speedDialType == NULL) ? array() : $speedDialList->speedDialType
					)); ?>
				</div>
				<div id="tab-3" class="tab-content <?= (3 == $tab) ? 'current' : ''; ?>"><?
					$head = simplexml_load_string($callHistory->schema);
					$body = simplexml_load_string($callHistory->any);
					$this->load->view('/pinlessAdmin/_callHistoryDollarPhone', array(
						'head' => $head, 'body' => $body
					)); ?>
				</div>
			</div><?
		} ?>
	</div>
</div>
<script src="<?= base_url('js/modules/pinless_admin.js'); ?>"></script>