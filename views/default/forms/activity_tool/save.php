<?php
$dbprefix = elgg_get_config('dbprefix');
// Get all Active Plugin List
$sql = "SELECT m.`value` as `plugin_id` FROM `{$dbprefix}entities` e,`{$dbprefix}metadata` m WHERE e.`subtype`='plugin' AND e.`guid`= m.`entity_guid` ORDER BY `value`";
$dbrow = elgg()->db->getData($sql);
$options[''] = "";
foreach($dbrow as $row){
	$options[$row->plugin_id] = "$row->plugin_id";
}
unset($sql);
unset($dbrow);
// Get Mapping
$sql = "SELECT DISTINCT p.`id`,e.`type`,e.`subtype`,p.`plugin_id`
 					FROM `{$dbprefix}entities` e 
					LEFT JOIN `{$dbprefix}entity_plugin_mapping` p 
						ON e.`type` = p.`type` AND e.`subtype` = p.`subtype` 
					WHERE e.`type` NOT IN ('site','user') AND e.`subtype` NOT IN ('plugin','elgg_upgrade','widget', 'comment')
					ORDER BY e.`type`,e.`subtype`";
$dbrow = elgg()->db->getData($sql);

echo "<table class='elgg-list elgg-table'>
				<thead>
					<tr>
						<th>Type</th>
						<th>SubType</th>
						<th>Plugin ID</th>
						<th></th>
					</tr>
				</thead>
				<tbody>";

foreach($dbrow as $row){
	if($row->plugin_id == null || $row->plugin_id == ""){
		$bgclor = "red";
	}
	echo "<tr class='elgg-item' bgcolor='$bgclor'>";
	echo "<td>$row->type</td><td>$row->subtype</td>";
	echo "<td>";
	echo "<input type='hidden' value='$row->type' name='type[]'>";
	echo "<input type='hidden' value='$row->subtype' name='subtype[]'>";
	echo elgg_view('input/select', [
				   'required' => true,
				   'name' => 'plugin_id[]',
				   'options_values' =>$options,
					 'value' => $row->plugin_id,
				]);
	echo "</td>";
	echo "<td>";
	echo elgg_format_element('a', [
			'class' => 'elgg-anchor',
			'href' => elgg_add_action_tokens_to_url(elgg_get_site_url()."action/activity_tool/delete?id={$row->id}"),
			'rel' => 'nofollow',
			'data-confirm' => elgg_echo('deleteconfirm:plural'),
		], elgg_format_element('span', [
			'class' => 'elgg-anchor-label',
		], elgg_echo('delete')));

	echo "</td>";
	echo "</tr>";
}

echo "</tbody>
		</table>";
		
echo elgg_view('input/submit', [
		'value' => elgg_echo('submit'),
		'name' => 'submit',
		'class' => 'elgg-button-submit mls',
	]);
?>
