<ul class="list-group width250">
	<form class="noPadding"
		  action="<?php echo base_url('pinlessAdminCtrl/addAni'); ?>"
		  method="GET">

		<input type="hidden" name="ani" value="<?php echo $ani; ?>" />
		<input type="hidden" name="offeringId" value="<?php echo $account->OfferingId; ?>" />
		<input type="hidden" name="pin" value="<?php echo $account->Pin; ?>" />

		<table>
			<tbody>
				<tr>
					<td>
						<input class="noMargin onlynumbers width250"
							   type="tel"
							   name="new_ani"
							   id="new_ani"
							   value=""
							   maxlength="15"
							   placeholder="<?php echo lang('new_phone'); ?>"
							   required />
					</td>
					<td>
						<input type="submit" value="<?php echo lang('add_associated_phone_btn'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>

	<br />

	<?
	$aniList = $registered_numbers->NewDataSet->ani;
	foreach($aniList as $item): ?>

	<li class="list-group-item">

		<?php echo $item->ANI; ?>

		<?php if(TRUE === $item['primary']): ?>
		<span class="badge badge-primary"><?php echo lang('primary'); ?></span>
		<?php else: ?>
		<span class="badge badge-transparent">
			<a class="delete_registered_number red"
			   href="<?php echo base_url('pinlessAdminCtrl/removeAni?pin=' . $account->Pin . '&ani=' . $ani . '&ani_delete=' . $item->ANI . '&offeringId=' . $account->OfferingId); ?>">
			   <?php echo lang('delete_link'); ?>
			</a>
		</span>
		<?php endif; ?>

	</li>

	<?php endforeach; ?>

</ul>