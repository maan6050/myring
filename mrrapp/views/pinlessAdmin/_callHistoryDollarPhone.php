<table class="width100Percent">
	<caption class="fontBold orange"><?php echo lang('last_calls'); ?></caption>
	<thead>
		<tr><?
			$elementNames = array_map('strval', $head->xpath('//xs:element[not(node())]/@name'));
			foreach($elementNames as $value)
			{
				echo'<th>'.$value.'</th>';
			} ?>
		</tr>
	</thead>
	<tbody>
		<tr><?
			$elementNames = array_map('strval', $body->xpath('//diffgr:diffgra[not(node())]/@name'));
			foreach($elementNames as $value)
			{
				echo'<td>' . $value . '</td>';
			}
			?>

		</tr>

	</tbody>
</table>
