<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?
if(isset($message))
{ ?>
	<div class="phonePane">
		<?= $message; ?>
	</div><?
}
if(isset($error))
{ ?>
	<div class="phonePane error">
		<?= $error; ?>
	</div><?
} ?>
<div class="centeredContent">
	<div class="formContainer">
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th></th><th></th>
				</tr>
			</thead>
			<tbody><?
				if($latest)
				{
					$amountCalculated = ($latest->amount - $latest->includeCharge) + ($latest->serviceCharge + $latest->includeCharge); ?>
					<tr>
						<td>Latest transaction on <strong><?= $latest->created; ?></strong> by <strong><?= $latest->name; ?></strong> to <strong><?= $latest->phone; ?></strong> for <strong>$<?= $amountCalculated; ?></strong> dollars.</td>
						<td class="actionsTd"><a href="<?= base_url('admin/transactionsList'); ?>">See all</a></td>
					</tr><?
				} ?>
				<tr>
					<td>Number of registered stores: <strong><?= $stores; ?></strong>.</td>
					<td class="actionsTd"><a href="<?= base_url('admin/storesList'); ?>">See all</a></td>
				</tr>
				<tr>
					<td colspan="2">Total amount due: <strong>$<?= $sumBalances; ?></strong>.</td>
				</tr>
				<tr>
					<td colspan="2">Sum of transactions this month: <strong>$<?= $sumTransactions; ?></strong>.</td>
				</tr>
				<tr>
					<td colspan="2">Amount of transactions this month: <strong><?= $numTransactions; ?></strong>.</td>
				</tr>
				<tr>
					<td colspan="2">Sum of deposits this month: <strong>$<?= $sumDeposits; ?></strong>.</td>
				</tr>
				<tr>
					<td colspan="2">Amount of deposits this month: <strong><?= $numDeposits; ?></strong>.</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="clear"></div>
</div>
