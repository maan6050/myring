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
		<div class="searchDiv">

            <?php  $this->load->view('/share/_alert'); ?>

			<form name="search" id="search" action="<?= base_url('reportsCtrl/invoices'); ?>" method="post">
				<input type="date" name="from" value="<?= isset($from) ? $from : ''; ?>" placeholder="" />
				<input type="date" name="to" value="<?= isset($to) ? $to : ''; ?>" placeholder="" />

                <?php if (isset($stores) && is_array($stores)): ?>

				<select id="store" name="store">

				    <?php if (count($stores) > 0): ?>
						
						<option value=""><?= lang('option_select_store'); ?> </option>

						<?php foreach ($stores as $item): ?>

						<option value="<?= $item->id?>" <?= (isset($store) && $store == $item->id) ? 'selected' : ''; ?>><?= $item->name?></option>

						<?php endforeach; ?>

					<?php else: ?>

						<option value=""><?= lang('option_select_store'); ?>  </option>

					<?php endif; ?>

				</select>

				<?php else: ?>

                <input type="hidden" name="store" value="<?= isset($stores) ? $stores : ''; ?>" />

                <?php endif; ?>

				<input type="submit" value="<?= lang('bttn_search'); ?>">
			</form>
		</div>

		<?php if (isset($items) && count($items) > 0): ?>

        <div class="alignRight">
			<form action="<?= base_url('reportsCtrl/generateInvoicePDF'); ?>" 
			      class="noPadding" 
				  method="POST" 
				  name="generateInvoicePDF" 
				  target="_blank">
				<input type="hidden" name="store" value="<?= $store; ?>">
				<input type="hidden" name="from" value="<?= $from; ?>">
				<input type="hidden" name="to" value="<?= $to; ?>">
				<input type="submit" value="<?= lang('bttn_print_pdf'); ?>">
			</form>
		</div>

		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?= lang('col_country'); ?></th>
					<th width="35%"><?= lang('col_product'); ?></th>
					<th><?= lang('col_transactions'); ?></th>
					<th><?= lang('col_face_value'); ?></th>
					<th><?= lang('col_comm'); ?></th>
					<th>Sub Total</th>
				</tr>
			</thead>
			<tbody>

			    <?php foreach($items as $item): ?>

				<tr>
					<td><?= $item->country; ?></td>
					<td><?= $item->product_name; ?></td>
					<td class="alignCenter"><?= $item->product_count; ?></td>
					<td class="alignRight"><?= moneyFormat($item->amount_sum); ?></td>
					<td class="alignRight"><?= moneyFormat($item->profit_sum); ?></td>
					<td class="alignRight"><?= moneyFormat($item->amount_sum - $item->profit_sum); ?></td>
				</tr>
				
				<?php endforeach; ?>

				<tr>
					<td class="alignRight" colspan="2"><strong>Totals:</strong></td>
					<td class="alignCenter"><strong><?= $transactions_total; ?></strong></td>
					<td class="alignRight"><strong><?= moneyFormat($facevalue_total); ?></strong></td>
					<td class="alignRight"><strong><?= moneyFormat($commission_total); ?></strong></td>
					<td class="alignRight"><strong><?= moneyFormat($facevalue_total - $commission_total); ?></strong></td>
				</tr>
			</tbody>
		</table>

		<?php else: ?>

			<div><?= lang('no_transactions_found'); ?></div>

		<?php endif; ?>

	</div>
</div>
