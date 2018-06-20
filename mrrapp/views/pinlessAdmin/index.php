<? $sectionTitle = lang('section_title'); ?>
<div class="centeredContent adminPage" id="pinless-admin">
	<h1><?= $sectionTitle; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>"><?= lang('breadcrumb_home'); ?></a> / <?= $sectionTitle; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="clear"></div>
		<form id="pinless_accounts_form" name="pinless_accounts_form" action="<?= base_url('pinlessAdminCtrl/info'); ?>" method="GET">
			<label for="pinless_product_code"><?= lang('product_label'); ?></label>
			<select name="code" id="pinless_product_code">
				<option value="myring" selected="selected"><?= lang('myring_product_name'); ?></option>
				<option value="ririce"><?= lang('ririce_product_name'); ?></option>
				<option value="DPPINLESS-30175660">My Ring Pinless International</option>
			</select>
			<label for="pinless_accounts_ani"><?= lang('phone_label'); ?></label>
			<input class="onlynumbers" type="tel" name="ani" id="pinless_accounts_ani" value="" maxlength="10" required>
			<div class="buttonsDiv">
				<input type="submit" value="<?= lang('pinless_accounts_btn'); ?>" />
			</div>
		</form>
	</div>
</div>
<script src="<?= base_url('js/modules/pinless_admin.js'); ?>"></script>