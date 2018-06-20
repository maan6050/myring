<table class="width100Percent">
	<caption class="fontBold orange"><?php echo lang('last_calls'); ?></caption>
	<thead>
		<tr>
			<th><?php echo lang('th_date'); ?></th>
			<th><?php echo lang('th_destiny_number'); ?></th>
			<th><?php echo lang('th_destiny'); ?></th>
			<th><?php echo lang('th_time'); ?></th>
			<th><?php echo lang('th_cost'); ?></th>
		</tr>
	</thead>
	<tbody>

	<?php foreach($call_history as $item): ?>

		<tr>
			<td><?php echo $item['start_time']; ?></td>
			<td><?php echo $item['destination']; ?></td>
			<td><?php echo $item['cost_code']; ?></td>
			<td><?php echo $item['duration'] . ' ' . lang('min'); ?></td>
			<td><?php echo moneyFormat($item['cost']); ?></td>
		</tr>

	<?php endforeach; ?>

	</tbody>
</table>
