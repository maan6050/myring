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
			<div class="clear"></div>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Store</th><th>Transactions</th><th>Total due</th><th>Total deposits</th><th>Balance must be</th><th>Current balance</th><th>Actions</th>
				</tr>
			</thead>
			<tbody><?
				if(count($stores) > 0)
				{
					foreach($stores as $store)
					{ ?>
						<tr class="alignRight">
							<td align="left"><?= $store->name; ?></td><td align="center"><?= $store->transactions; ?></td><td>$<?= $store->totalDue; ?></td><td>$<?= $store->deposits; ?></td><td>$<?= $store->realBalance; ?></td><td>$<?= $store->balance; ?></td>
							<td class="actionsTd">
								<a href="#" data-id="<?= $store->id; ?>" class="updateItem"><i class="fa fa-retweet" aria-hidden="true"></i>Change</a>
							</td>
						</tr><?
					}
				}
				else
				{ ?>
					<tr><td colspan="7">All balances seem ok.</td></tr><?
				} ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript"><?
	if(isset($msg))
	{ ?>
		alert('<?= $msg; ?>'); <?
	} ?>

	jQuery(document).ready(function($){
		$(document).on('click', '.updateItem', function() {
			if(confirm('Are you sure you want to update this store\'s balance?')){
				window.location = '<?= base_url('report/reconcile/'); ?>' + $(this).attr('data-id');
			}
		});
	});
</script>
