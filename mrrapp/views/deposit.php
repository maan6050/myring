<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="deposit" id="deposit" action="<?= base_url('depositCtrl'); ?>" method="post">
			<label for="clientId">Client: </label>
			<select id="clientId" name="clientId" required>
				<option value="" data-max="0"></option><?
				foreach($clients as $client)
				{ ?>
					<option value="<?= $client->id; ?>" data-max="<?= $client->balance; ?>"><?= $client->name; ?></option><?
				} ?>
			</select>
			<label for="maxDeposit">Maximum deposit allowed: </label>
			<input type="number" id="maxDeposit" name="maxDeposit" value="0" disabled>
			<label for="paymentMethod">Payment method: </label>
			<select id="paymentMethod" name="paymentMethod">
				<option value="a">ACH Portal</option>
				<option value="d">Direct distributor</option>
				<option value="s">Direct subdistributor</option>
				<option value="p">Credit card portal</option>
			</select>
			<label for="reference">Reference: </label>
			<input type="text" id="reference" name="reference" placeholder="Reference">
			<label for="amount">Amount: </label>
			<input type="number" id="amount" name="amount" placeholder="Amount" required>
			<label for="comments">Comments: </label>
			<textarea id="comments" name="comments"></textarea>
			<div class="buttonsDiv">
				<input type="submit" value="Make deposit">
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
		$('#deposit').validate({
			rules: {
				amount: {
					min: 0,
					max: 0
				}
			},
			errorPlacement: function(error, element){
				// Append error within linked label
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});

		$('#clientId').on('change', function(){
			var maxDeposit = Number($('#clientId option:selected').attr('data-max'));
			$('#maxDeposit').val(maxDeposit);  // Muestro el máximo valor que se puede depositar.
			$('#amount').rules('remove', 'max');
			$('#amount').rules('add', {
				max: maxDeposit  // Valido que no sobrepase el máximo.
			});
		});
	});
</script>
