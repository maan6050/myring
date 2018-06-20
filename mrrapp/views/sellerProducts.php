<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <a href="<?= base_url('admin/sellersList'); ?>">Agents list</a> / <?= $title.' - '.$seller->name; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?> - <strong><?= $seller->name; ?></strong></h3>
			<div class="clear"></div>
		</div>
		<form name="cSellerProducts" id="cSellerProducts" action="<?= base_url('admin/sellerProducts/'.$seller->id); ?>" method="post"><?
			foreach($products as $product)
			{
				if($countryName != $product->countryName)
				{
					$countryName = $product->countryName; ?>
					<div class="fcHeader borderBottom subTitle">
						<h3><?= $countryName; ?></h3>
						<div class="clear"></div>
					</div><?
				} ?>
				<label for="profit[<?= $product->id; ?>]"><?= $product->name; ?>: </label>
				<input type="number" id="profit[<?= $product->id; ?>]" name="profit[<?= $product->id; ?>]" value="<?= $product->defaultUserProfit; ?>" placeholder="Fee percentage" required><?
			} ?>
			<div class="buttonsDiv">
				<input type="submit" value="Update">
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
		$('#cSellerProducts').validate({
			errorPlacement: function(error, element){
				// Append error within linked label
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});
	});
</script>
