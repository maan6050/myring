<form class="noPadding"
	  id="update_speed_dials_form"
	  name="update_speed_dials_form"
	  action="<?php echo base_url('pinlessAdminCtrl/updateSpeedDeals'); ?>" method="POST">

	<input type="hidden" name="ani" value="<?php echo $ani; ?>" />
	<input type="hidden" name="code" value="<?php echo $code; ?>" />

	<table class="width100Percent">
		<caption class="fontBold orange"><?php echo lang('speed_dials_info'); ?></caption>
		<thead>
			<tr>
				<th><?php echo lang('th_position'); ?></th>
				<th><?php echo lang('th_destiny_number'); ?></th>
				<th><?php echo lang('th_description'); ?></th>
				<th><?php echo lang('th_direct_access'); ?></th>
			</tr>
		</thead>
		<tbody>

		<?php foreach($speed_dials as $key => $item): ?>

			<tr>
				<td><?php echo $item['position']; ?></td>
				<td>
					<input class="noMargin onlynumbers width200"
							type="tel"
							name="speed_dials[<?php echo $key; ?>][telephone]"
							value="<?php echo $item['telephone']; ?>" />
				</td>
				<td>
					<input class="noMargin width200"
							type="tel"
							name="speed_dials[<?php echo $key; ?>][description]"
							value="<?php echo $item['description']; ?>" />
				</td>
				<td><?php echo $item['access_number']; ?></td>
			</tr>

		<?php endforeach; ?>

		</tbody>
	</table>
	<br />
	<div class="buttonsDiv">
		<input id="update_speed_dials_btn" type="button" value="<?php echo lang('update_speed_dials_btn'); ?>" />
	</div>
</form>
