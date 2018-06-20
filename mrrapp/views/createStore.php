<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <a href="<?= base_url('admin/storesList'); ?>">Stores</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="cClient" id="cClient" action="<?= base_url($labels['action']); ?>" method="post">
			<input type="hidden" name="id" value="<? if(isset($selClient->id)) echo $selClient->id; ?>">
			<label for="userId">Agent in charge: </label>
			<select id="userId" name="userId"><?
				foreach($agents as $agent)
				{ ?>
					<option value="<?= $agent->id; ?>"<? if(isset($selClient->userId) && $selClient->userId == $agent->id) echo ' selected'; ?>><?= $agent->name; ?></option><?
				} ?>
			</select>
			<label for="name">Name: </label>
			<input type="text" id="name" name="name" value="<? if(isset($selClient->name)) echo $selClient->name; ?>" placeholder="Name" required>
			<label for="username">Username: </label>
			<input type="text" id="username" name="username" value="<? if(isset($selClient->username)) echo $selClient->username; ?>" placeholder="Username" maxlength="20" required>
			<label for="password"><?= $labels['pw']; ?> </label>
			<input type="text" id="password" name="password" placeholder="Password" <?= $labels['req']; ?>>
			<label for="paymentMethod">Payment method: </label>
			<select id="paymentMethod" name="paymentMethod">
				<option value="c"<? if(isset($selClient->paymentMethod) && $selClient->paymentMethod == 'c') echo ' selected'; ?>>Credit</option>
				<option value="a"<? if(isset($selClient->paymentMethod) && $selClient->paymentMethod == 'a') echo ' selected'; ?>>ACH</option>
			</select>
			<label for="maxBalance">Maximum balance: </label>
			<input type="number" id="maxBalance" name="maxBalance" value="<? if(isset($selClient->maxBalance)) echo $selClient->maxBalance; ?>" placeholder="Balance" <?= $labels['maxBalance']; ?>>
			<div class="fcHeader borderBottom subTitle">
				<h3>Contact information</h3>
				<div class="clear"></div>
			</div>
			<label for="contactName">Name: </label>
			<input type="text" id="contactName" name="contactName" value="<? if(isset($selClient->contactName)) echo $selClient->contactName; ?>" placeholder="Name" required>
			<label for="email">Email: </label>
			<input type="email" id="email" name="email" value="<? if(isset($selClient->email)) echo $selClient->email; ?>" placeholder="Email">
			<label for="phone">Phone: </label>
			<input type="text" id="phone" name="phone" value="<? if(isset($selClient->phone)) echo $selClient->phone; ?>" placeholder="Phone" required>
			<label for="address">Address: </label>
			<input type="text" id="address" name="address" value="<? if(isset($selClient->address)) echo $selClient->address; ?>" placeholder="Address">
			<label for="city">City: </label>
			<input type="text" id="city" name="city" value="<? if(isset($selClient->city)) echo $selClient->city; ?>" placeholder="City">
			<label for="zip">Zip: </label>
			<input type="text" id="zip" name="zip" value="<? if(isset($selClient->zip)) echo $selClient->zip; ?>" placeholder="Zip code">
			<label for="state">State: </label>
			<input type="text" id="state" name="state" value="<? if(isset($selClient->state)) echo $selClient->state; ?>" placeholder="State">
			<label for="country">Country: </label>
			<input type="text" id="country" name="country" value="<? if(isset($selClient->country)) echo $selClient->country; ?>" placeholder="Country">
			<label for="timezone">Time zone: </label>
			<select name="timezone"><?
				for($i=12; $i>0; $i--)
				{
					$utc = $i < 10 ? '-0'.$i : '-'.$i;
					$utc .= ':00'; ?>
					<option value="<?= $utc; ?>"<? if(isset($selClient->timezone) && $selClient->timezone == $utc) echo ' selected'; ?>><?= $utc; ?></option><?
				} ?>
				<option value="00:00"<? if(isset($selClient->timezone) && $selClient->timezone == '00:00') echo ' selected'; ?>>&nbsp;00:00</option><?
				for($i=1; $i<=12; $i++)
				{
					$utc = $i < 10 ? '+0'.$i : '+'.$i;
					$utc .= ':00'; ?>
					<option value="<?= $utc; ?>"<? if(isset($selClient->timezone) && $selClient->timezone == $utc) echo ' selected'; ?>><?= $utc; ?></option><?
				} ?>
			</select>
			<div class="buttonsDiv">
				<input type="submit" value="<?= $labels['btn']; ?>">
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#cClient').validate({
			errorPlacement: function(error, element){
				// Append error within linked label
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});

		$('#paymentMethod').on('change', function(){
			if($(this).val() == 'a'){
				$('#maxBalance').val(0);
				$('#maxBalance').attr('readonly', 'readonly');
			}else{
				$('#maxBalance').attr('readonly', null);
			}
		});
	});
</script>
