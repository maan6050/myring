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
			<a href="#" class='deleteAll'><?= lang('delete_all'); ?></a>
			<div class="clear"></div>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?= lang('col_product'); ?></th>
					<th><?= lang('recipients_phone'); ?></th>
					<th><?= lang('options'); ?></th>
				</tr>
			</thead>
			<tbody><?
				if(count($phones) > 0)
				{
					foreach($phones as $phone)
					{ ?>
						<tr>
							<td><?= $phone->productName; ?></td>
							<td><?= $phone->phone; ?></td>
							<td class="actionsTd">
								<a href="#" data-id="<?= $phone->phone.'/'.$phone->productId; ?>" class="deleteItem"><i class="fa fa-times" aria-hidden="true"></i><?= lang('delete'); ?></a>
							</td>
						</tr><?
					}
				}
				else
				{ ?>
					<tr><td colspan="3"><?= lang('no_numbers'); ?></td></tr><?
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
		$(document).on('click', '.deleteItem', function() {
			if(confirm('<?= lang('confirm_single'); ?>')){
				window.location = '<?= base_url('report/guestNumberDelete/'); ?>' + $(this).attr('data-id');
			}
		});
		$(document).on('click', '.deleteAll', function() {
			if(confirm('<?= lang('confirm_all'); ?>')){
				window.location = '<?= base_url('report/guestsNumbersDeleteAll'); ?>';
			}
		});
	});
</script>
