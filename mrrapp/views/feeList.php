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
			<h3><strong><?= $client->name; ?></strong></h3>
			<div class="clear"></div>
		</div><?
		foreach($products as $product)
		{
			if($product->isFirst)
			{ ?>
				<div class="fcHeader borderBottom subTitle">
					<h3><?= $product->countryName; ?></h3>
					<div class="clear"></div>
				</div>
				<table cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?= lang('col_product'); ?></th>
							<th style="text-align:right;"><?= lang('col_fee_percentage'); ?></th>
						</tr>
					</thead>
					<tbody><?
			} ?>
			<tr>
				<td><?= $product->name; ?></td>
				<td style="text-align:right;"><?= (float)$product->defaultProfit; ?>%</td>
			</tr><?
			if($product->isLast)
			{ ?>
					</tbody>
				</table><?
			}
		} ?>
	</div>
</div>