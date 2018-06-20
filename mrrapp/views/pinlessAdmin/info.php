<?php
$section_title = lang('section_title');
$response_code = getArrayValue($response, 'Response-Code');

$primary_ani = getArrayValue($response, 'primary_ani');
$product_name = getArrayValue($response, 'product_name');
$current_balance = moneyFormat(getArrayValue($response, 'current_balance'));

$detail_text = FALSE;

if($primary_ani && $product_name && $current_balance)
{
    $detail_text = sprintf(lang('pinless_account_detail_text'), $primary_ani, $product_name, $current_balance);
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
	if($msg_code)
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
		if($response_code != 200)
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
					<li class="tab-link <?= (4 == $tab) ? 'current' : ''; ?>" data-tab="tab-4"><?= lang('tab_4'); ?></li>
				</ul>
				<div id="tab-1" class="tab-content <?= (1 == $tab) ? 'current' : ''; ?>"><?
					$this->load->view('/pinlessAdmin/_associated_phones', array(
						'ani' => getArrayValue($response, 'primary_ani'),
						'code' => getArrayValue($response, 'product_code'),
						'registered_numbers' => getArrayValue($response, 'registered_numbers')
					)); ?>
				</div>
				<div id="tab-2" class="tab-content <?= (2 == $tab) ? 'current' : ''; ?>"><?
					$this->load->view('/pinlessAdmin/_speed_dials', array(
						'ani' => getArrayValue($response, 'primary_ani'),
						'code' => getArrayValue($response, 'product_code'),
						'speed_dials' => getArrayValue($response, 'speed_dials')
					)); ?>
				</div>
				<div id="tab-3" class="tab-content <?= (3 == $tab) ? 'current' : ''; ?>"><?
					$this->load->view('/pinlessAdmin/_call_history', array(
						'call_history' => getArrayValue($response, 'call_history')
					)); ?>
				</div>
				<div id="tab-4" class="tab-content <?= (4 == $tab) ? 'current' : ''; ?>"><?
					$this->load->view('/pinlessAdmin/_language', array(
						'ani' => getArrayValue($response, 'primary_ani'),
						'code' => getArrayValue($response, 'product_code'),
						'language' => getArrayValue($response, 'language')
					)); ?>
				</div>
			</div><?
		} ?>
	</div>
</div>
<script src="<?= base_url('js/modules/pinless_admin.js'); ?>"></script>
