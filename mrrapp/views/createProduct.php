<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <a href="<?= base_url('admin/productsList'); ?>">Products</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<form name="cProduct" id="cProduct" action="<?= base_url('admin/'.$labels['action']); ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="id" value="<? if(isset($selProduct->id)) echo $selProduct->id; ?>"><?
			if(isset($countryId))
			{ ?>
				<input type="hidden" name="countryId" value="<?= $countryId; ?>">
				<label>Country: </label>
				<strong><?= $countryName; ?></strong>
				<div class="clear10"></div>
				<div class="clear10"></div><?
			}
			else
			{ ?>
				<label for="countryId">Country: </label>
				<select id="countryId" name="countryId" required><?
					foreach($countries as $country)
					{ ?>
						<option value="<?= $country->id; ?>"><?= $country->name; ?></option><?
					} ?>
				</select><?
			} ?>
			<label for="providerId">Provider: </label>
			<select id="providerId" name="providerId" required><?
				foreach($providers as $provider)
				{ ?>
					<option value="<?= $provider->id; ?>"<? if(isset($selProduct->providerId) && $selProduct->providerId == $provider->id) echo ' selected'; ?>><?= $provider->name; ?></option><?
				} ?>
			</select>
			<label for="offeringId">Offering ID: </label>
			<input type="text" id="offeringId" name="offeringId" value="<? if(isset($selProduct->offeringId)) echo $selProduct->offeringId; ?>" placeholder="Product Offering ID or SKU" required>
			<label for="isUnlimited">Is PIN: <em>Only applicable to DollarPhone products</em> </label>
			<select id="isPIN" name="isPIN">
				<option value="0"<? if(isset($selProduct->isPIN) && $selProduct->isPIN == '0') echo ' selected'; ?>>No</option>
				<option value="1"<? if(isset($selProduct->isPIN) && $selProduct->isPIN == '1') echo ' selected'; ?>>Yes</option>
			</select>
			<label for="isUnlimited">Is Unlimited: </label>
			<select id="isUnlimited" name="isUnlimited">
				<option value="0"<? if(isset($selProduct->isUnlimited) && $selProduct->isUnlimited == '0') echo ' selected'; ?>>No</option>
				<option value="1"<? if(isset($selProduct->isUnlimited) && $selProduct->isUnlimited == '1') echo ' selected'; ?>>Yes</option>
			</select>
			<label for="allowOpenAmount">Allow Open Amout: </label>
			<select id="allowOpenAmount" name="allowOpenAmount">
				<option value="0"<? if(isset($selProduct->allowOpenAmount) && $selProduct->allowOpenAmount == '0') echo ' selected'; ?>>No</option>
				<option value="1"<? if(isset($selProduct->allowOpenAmount) && $selProduct->allowOpenAmount == '1') echo ' selected'; ?>>Yes</option>
			</select>
			<label for="showAsList">Show As List: </label>
			<select id="showAsList" name="showAsList">
				<option value="0"<? if(isset($selProduct->showAsList) && $selProduct->showAsList == '0') echo ' selected'; ?>>No</option>
				<option value="1"<? if(isset($selProduct->showAsList) && $selProduct->showAsList == '1') echo ' selected'; ?>>Yes</option>
			</select>
			<label for="mnc">Mobile Network Code: <a href="https://en.wikipedia.org/wiki/Mobile_country_code" target="_blank">See all</a> </label>
			<input type="number" id="mnc" name="mnc" value="<? if(isset($selProduct->mnc)) echo $selProduct->mnc; ?>" placeholder="MNC" required>
			<label for="name">Name: </label>
			<input type="text" id="name" name="name" value="<? if(isset($selProduct->name)) echo $selProduct->name; ?>" placeholder="Name" required>
			<label for="serviceCharge">Charge for service: </label>
			<input type="number" id="serviceCharge" name="serviceCharge" value="<? if(isset($selProduct->serviceCharge)) echo $selProduct->serviceCharge; ?>" placeholder="Additional cost per transaction" required>
			<label for="includeCharge">Include charge: </label>
			<input type="number" id="includeCharge" name="includeCharge" value="<? if(isset($selProduct->includeCharge)) echo $selProduct->includeCharge; ?>" placeholder="Include cost per transaction">
			<label for="image"><?= $labels['image']; ?> <em>600px x 400px max.</em></label>
			<input type="file" id="image" name="image">
			<div class="fcHeader borderBottom subTitle">
				<h3>Fees and profits:</h3>
				<div class="clear"></div>
			</div>
			<label for="defaultProfit">Default store fee: </label>
			<input type="number" id="defaultProfit" name="defaultProfit" value="<? if(isset($selProduct->defaultProfit)) echo $selProduct->defaultProfit; ?>" placeholder="Top-up percentage claimed per store" required>
			<label for="defaultUserProfit">Default agent fee: </label>
			<input type="number" id="defaultUserProfit" name="defaultUserProfit" value="<? if(isset($selProduct->defaultUserProfit)) echo $selProduct->defaultUserProfit; ?>" placeholder="Top-up percentage claimed per seller" required>
			<label for="companyProfit">Company profit: </label>
			<input type="number" id="companyProfit" name="companyProfit" value="<? if(isset($selProduct->companyProfit)) echo $selProduct->companyProfit; ?>" placeholder="Profit percentage of the company" required>
			<div class="fcHeader borderBottom subTitle">
				<h3>Denominations:</h3>
				<div class="clear"></div>
			</div>
			<select id="type" name="type">
				<option value="f">Fixed</option>
				<option value="r"<? if(isset($selProduct->type) && $selProduct->type == 'r') echo ' selected'; ?>>Range</option>
			</select>
			<div id="cFixed"<?= $labels['displayFixed']; ?>>
				<label for="fixed">Fixed: </label>
				<input type="text" id="fixed" name="fixed" value="<? if(isset($selProduct->fixed)) echo $selProduct->fixed; ?>" placeholder="Comma separated values: 10,15,20,... or 5|6,10|11,15|17,..." required>
			</div>
			<div id="cRange"<?= $labels['displayRange']; ?>>
				<label for="rangeMin">Minimum range: </label>
				<input type="number" id="rangeMin" name="rangeMin" value="<? if(isset($selProduct->rangeMin)) echo $selProduct->rangeMin; ?>" placeholder="Minimum range" required>
				<label for="rangeMax">Maximum range: </label>
				<input type="number" id="rangeMax" name="rangeMax" value="<? if(isset($selProduct->rangeMax)) echo $selProduct->rangeMax; ?>" placeholder="Maximum range" required>
			</div>
			<label for="status">Status: </label>
			<select id="status" name="status">
				<option value="a">Active</option>
				<option value="i"<? if(isset($selProduct->status) && $selProduct->status == 'i') echo ' selected'; ?>>Inactive</option>
			</select>
			<div class="buttonsDiv">
				<input type="submit" value="<?= $labels['btn']; ?>">
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		jQuery.validator.addMethod('fixed', function(value, element) {
			var regexp1 = /^[0-9]+(.[0-9]{2})?(,[0-9]+(.[0-9]{2})?)*$/;
			var regexp2 = /^[0-9]+\|[0-9]+(,[0-9]+\|[0-9]+)*$/;
			return this.optional(element) || regexp1.test(value) || regexp2.test(value);
		}, 'Example: 5,10,15,20,25,30 or 5|6,10|11,15|17');

		$('#cProduct').validate({
			rules:{
				fixed:{
					fixed: true
				}
			},
			errorPlacement: function(error, element){
				// Append error within linked label
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});

		$('#type').on('change', function(){
			if($(this).val() == 'f'){
				$('#cFixed').fadeIn();
				$('#cRange').hide();
			}else{
				$('#cFixed').hide();
				$('#cRange').fadeIn();
			}
		});
	});
</script>
