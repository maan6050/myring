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
			<a href="<?= base_url('content/slideCreateForm'); ?>">Create slide</a>
			<div class="clear"></div>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Image</th><th>Actions</th>
				</tr>
			</thead>
			<tbody><?
				if(count($slides) > 0)
				{
					foreach($slides as $s)
					{ ?>
						<tr>
							<td><? if($s->image != '') { ?><img src="<?= base_url(UPLOADS.$s->image); ?>" height="50"><? } ?></td>
							<td class="actionsTd">
								<a href="#" data-id="<?= $s->id; ?>" class="deleteItem"><i class="fa fa-times" aria-hidden="true"></i>Delete</a>
							</td>
						</tr><?
					}
				}
				else
				{ ?>
					<tr>
						<td align="center" colspan="2">No slides found.</td>
					</tr><?
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
			if(confirm('Are you sure you want to remove this slide?')){
				window.location = '<?= base_url('content/slideDelete/'); ?>' + $(this).attr('data-id');
			}
		});
	});
</script>