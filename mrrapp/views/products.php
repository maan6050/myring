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
		<div class="fcHeader">
			<h3><?= $title; ?></h3>
			<a href="<?= base_url('admin/productCreateForm/'.$countryId); ?>">New product</a>
			<div class="clear"></div>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Country</th><th>Provider</th><th>Name</th><th>Image</th><th>Denominations</th><th>Defaults</th><th>Actions</th>
				</tr>
			</thead>
			<tbody><?
				if(count($products) > 0)
				{
					foreach($products as $product)
					{ ?>
						<tr>
							<td><?= $product->countryName; ?></td><td><?= $product->providerName; ?></td>
							<td>
								<em class="<?= $product->statusClass; ?>"><?= $product->name; ?></em><br>
								<em class="blue"><?= $product->offeringId; ?></em><br>
								<em class="green"><?= $product->mnc; ?></em>
							</td>
							<td><?= $product->image; ?></td>
							<td>
								<?= $product->denominations; ?><br>
								<em class="blue"><? if($product->serviceCharge != 0) echo 'Plus: $'.$product->serviceCharge; ?></em>
							</td>
							<td>
								Store: <span class="blue"><?= $product->defaultProfit; ?></span><br>
								Agent: <span class="green"><?= $product->defaultUserProfit; ?></span>
							</td>
							<td class="actionsTd">
								<a href="<?= base_url('admin/productEditForm/'.$product->id); ?>"><i class="fa fa-refresh" aria-hidden="true"></i>Update</a>
								<div class="clear10"></div>
								<a href="#" data-id="<?= $product->id; ?>" class="deleteItem"><i class="fa fa-times" aria-hidden="true"></i>Delete</a>
							</td>
						</tr><?
					}
				}
				else
				{ ?>
					<tr><td colspan="7">No products have been associated.</td></tr><?
				} ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript"><?
	if(isset($msg))
	{ ?>
		alert("<?= $msg; ?>"); <?
	} ?>

	jQuery(document).ready(function($){
		$(document).on('click', '.deleteItem', function() {
			if(confirm('Are you sure you want to remove this product?')){
		window.location = '<?= base_url('admin/productDelete/'); ?>' + $(this).attr('data-id') + '<? if($countryId != NULL) echo '/'.$countryId; ?>';
			}
		});
	});
</script>
