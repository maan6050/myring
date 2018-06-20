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
		<form name="account" id="account" action="<?= base_url(uri_string()); ?>" method="post">
			<label for="name"><?= lang('label_name'); ?></label>
			<input type="text" id="name" name="name" value="<? if(isset($selected->name)) echo $selected->name; ?>" placeholder="<?= lang('placeholder_name'); ?>" required>
			<label for="username"><?= lang('label_username'); ?></label>
			<input type="text" id="username" name="username" value="<? if(isset($selected->username)) echo $selected->username; ?>" placeholder="<?= lang('placeholder_username'); ?>" maxlength="20" required>
			<div class="fcHeader borderBottom subTitle">
				<h3><?= lang('contact_info'); ?></h3>
				<div class="clear"></div>
			</div>
			<label for="contactName"><?= lang('label_name'); ?></label>
			<input type="text" id="contactName" name="contactName" value="<? if(isset($selected->contactName)) echo $selected->contactName; ?>" placeholder="<?= lang('placeholder_name'); ?>" required>
			<label for="email"><?= lang('label_email'); ?></label>
			<input type="email" id="email" name="email" value="<? if(isset($selected->email)) echo $selected->email; ?>" placeholder="<?= lang('placeholder_email'); ?>">
			<label for="phone"><?= lang('label_phone'); ?></label>
			<input type="text" id="phone" name="phone" value="<? if(isset($selected->phone)) echo $selected->phone; ?>" placeholder="<?= lang('placeholder_phone'); ?>" required>
			<label for="address"><?= lang('label_address'); ?></label>
			<input type="text" id="address" name="address" value="<? if(isset($selected->address)) echo $selected->address; ?>" placeholder="<?= lang('placeholder_address'); ?>">
			<label for="city"><?= lang('label_city'); ?></label>
			<input type="text" id="city" name="city" value="<? if(isset($selected->city)) echo $selected->city; ?>" placeholder="<?= lang('placeholder_city'); ?>">
			<label for="zip"><?= lang('label_zip'); ?></label>
			<input type="text" id="zip" name="zip" value="<? if(isset($selected->zip)) echo $selected->zip; ?>" placeholder="<?= lang('placeholder_zip'); ?>">
			<label for="state"><?= lang('label_state'); ?></label>
			<input type="text" id="state" name="state" value="<? if(isset($selected->state)) echo $selected->state; ?>" placeholder="<?= lang('placeholder_state'); ?>">
			<label for="country"><?= lang('label_country'); ?></label>
			<input type="text" id="country" name="country" value="<? if(isset($selected->country)) echo $selected->country; ?>" placeholder="<?= lang('placeholder_country'); ?>">
			<label for="timezone"><?= lang('label_timezone'); ?></label>
			<select name="timezone"><?
				for($i=12; $i>0; $i--)
				{
					$utc = $i < 10 ? '-0'.$i : '-'.$i;
					$utc .= ':00'; ?>
					<option value="<?= $utc; ?>"<? if(isset($selected->timezone) && $selected->timezone == $utc) echo ' selected'; ?>><?= $utc; ?></option><?
				} ?>
				<option value="00:00"<? if(isset($selected->timezone) && $selected->timezone == '00:00') echo ' selected'; ?>>&nbsp;00:00</option><?
				for($i=1; $i<=12; $i++)
				{
					$utc = $i < 10 ? '+0'.$i : '+'.$i;
					$utc .= ':00'; ?>
					<option value="<?= $utc; ?>"<? if(isset($selected->timezone) && $selected->timezone == $utc) echo ' selected'; ?>><?= $utc; ?></option><?
				} ?>
			</select>
			<div class="fcHeader borderBottom subTitle">
				<h3><?= lang('update_password'); ?> - <em><?= lang('optional'); ?></em></h3>
				<div class="clear"></div>
			</div>
			<label for="password"><?= lang('label_new_pass'); ?> </label>
			<input type="password" id="password" name="password" placeholder="<?= lang('placeholder_new_pass'); ?>">
			<label for="password"><?= lang('label_conf_new_pass'); ?></label>
			<input type="password" id="passwordConf" name="passwordConf" placeholder="<?= lang('placeholder_conf_new_pass'); ?>">
			<div class="buttonsDiv">
				<input type="submit" value="<?= lang('bttn_update'); ?>">
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
