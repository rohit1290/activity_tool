<?php


/************************************************************************************************************************
Enable/Disable User Activity on Plugin Activation/Deactivation
************************************************************************************************************************/

elgg_register_event_handler('init', 'system', 'plugin_activity_fix_init');

function plugin_activity_fix_init() {
	elgg_register_event_handler('deactivate', 'plugin', '_elgg_river_generic_deactivate');
	elgg_register_event_handler('activate', 'plugin', '_elgg_river_generic_activate');
	
	elgg_register_event_handler('deactivate:after', 'plugin', '_elgg_river_on_plugin_disable');
	elgg_register_event_handler('activate:after', 'plugin', '_elgg_river_on_plugin_enable');
	
	elgg_register_menu_item('page', [
		'name' => 'administer_utilities:plugin_river_fix',
		'text' => elgg_echo('admin:administer_utilities:plugin_river_fix'),
		'href' => 'admin/administer_utilities/plugin_river_fix',
		'section' => 'administer',
		'parent_name' => 'administer_utilities',
		'context' => 'admin',
	]);
	
	elgg_extend_view("admin/dashboard", "activity_tool/alert_div", 1);
	// elgg_extend_view("admin/plugins","activity_tool/alert_div",1);
	
	if (elgg_get_plugin_setting('activity_on_user_ban', 'activity_tool') == "yes") {
		// Hide River Activity on User Ban
		elgg_register_event_handler('ban', 'user', '_elgg_river_disable');
		// Show River Activity on User (Un)Ban
		elgg_register_event_handler('unban', 'user', '_elgg_river_enable');
	}
	
}

function _elgg_river_generic_deactivate(\Elgg\Event $event) {
	$entity = $event->getObject();
	_elgg_services()->events->triggerAfter('deactivate', 'plugin', $entity);
	return true;
}

function _elgg_river_generic_activate(\Elgg\Event $event) {
	$entity = $event->getObject();
	_elgg_services()->events->triggerAfter('activate', 'plugin', $entity);
	return true;
}

function _elgg_river_on_plugin_disable(\Elgg\Event $event) {
	$entity = $event->getObject();
	if (!isset($entity['plugin_id'])) {
		return;
	}
	_elgg_river_on_activate_deactivate_plugin($entity, 'no');
	return true;
}

function _elgg_river_on_plugin_enable(\Elgg\Event $event) {
	$entity = $event->getObject();
	if (!isset($entity['plugin_id'])) {
		return;
	}
	_elgg_river_on_activate_deactivate_plugin($entity, 'yes');
	return true;
}

function _elgg_river_on_activate_deactivate_plugin($entity, $enable_option) {
	$dbprefix = elgg_get_config('dbprefix');
	$plugin = $entity['plugin_id'];
	
	if ($enable_option == "yes") {
		// activate the plugin
		$operator = "AND";
		$enable = "= 'yes'";
	} else {
		// deactivate the plugin
		$operator = "OR";
		$enable = "IS NULL";
	}
	/*
	SELECT rv.id, rv.action_type, rv.view, 
		rv.object_guid, epm2.plugin_id as `object_plugin_id`, sub2.enabled,
		rv.target_guid, epm3.plugin_id as `target_plugin_id`, sub3.enabled,
		ce.container_guid, epm4.plugin_id as `container_plugin_id`, sub4.enabled
			FROM ck_river AS rv
			LEFT JOIN ck_entities AS oe ON oe.guid = rv.object_guid
			LEFT JOIN ck_entities AS te ON te.guid = rv.target_guid
			LEFT JOIN ck_entities AS ce ON oe.container_guid = ce.guid
			
			LEFT JOIN ck_entity_plugin_mapping AS epm2 ON (epm2.type=oe.type AND epm2.subtype = oe.subtype)
			LEFT JOIN ck_entity_plugin_mapping AS epm3 ON (epm3.type=te.type AND epm3.subtype = te.subtype)   
			LEFT JOIN ck_entity_plugin_mapping AS epm4 ON (epm4.type=ce.type AND epm4.subtype = ce.subtype)   

			LEFT JOIN (SELECT value,enabled FROM `ck_entity_relationships` b JOIN `ck_metadata` c on b.relationship = "active_plugin" AND c.entity_guid = b.guid_one) AS sub2 ON (sub2.value=epm2.plugin_id)
			LEFT JOIN (SELECT value,enabled FROM `ck_entity_relationships` b JOIN `ck_metadata` c on b.relationship = "active_plugin" AND c.entity_guid = b.guid_one) AS sub3 ON (sub3.value=epm3.plugin_id)
			LEFT JOIN (SELECT value,enabled FROM `ck_entity_relationships` b JOIN `ck_metadata` c on b.relationship = "active_plugin" AND c.entity_guid = b.guid_one) AS sub4 ON (sub4.value=epm4.plugin_id)
		WHERE 
			(
				(epm2.plugin_id = 'blog') OR
				(epm3.plugin_id = 'blog') OR
				(epm4.plugin_id = 'blog')
			) AND (
				((sub2.enabled = "yes") OR (epm2.plugin_id IS NULL)) AND
				((sub3.enabled = "yes") OR (epm3.plugin_id IS NULL)) AND
				((sub4.enabled = "yes") OR (epm4.plugin_id IS NULL)) AND
				((sub5.enabled = "yes") OR (epm5.plugin_id IS NULL))
			)
		*/

	$query = <<<QUERY
UPDATE {$dbprefix}river AS rv
	LEFT JOIN {$dbprefix}entities AS oe ON oe.guid = rv.object_guid
	LEFT JOIN {$dbprefix}entities AS te ON te.guid = rv.target_guid
	LEFT JOIN {$dbprefix}entities AS ce ON oe.container_guid = ce.guid
	LEFT JOIN {$dbprefix}entities AS ce2 ON ce.container_guid = ce2.guid
	
	LEFT JOIN {$dbprefix}entity_plugin_mapping AS epm2 ON (epm2.type=oe.type AND epm2.subtype = oe.subtype)
	LEFT JOIN {$dbprefix}entity_plugin_mapping AS epm3 ON (epm3.type=te.type AND epm3.subtype = te.subtype)   
	LEFT JOIN {$dbprefix}entity_plugin_mapping AS epm4 ON (epm4.type=ce.type AND epm4.subtype = ce.subtype)   
	LEFT JOIN {$dbprefix}entity_plugin_mapping AS epm5 ON (epm5.type=ce2.type AND epm5.subtype = ce2.subtype)   

	LEFT JOIN (SELECT value,enabled FROM `{$dbprefix}entity_relationships` b JOIN `{$dbprefix}metadata` c on b.relationship = "active_plugin" AND c.entity_guid = b.guid_one) AS sub2 ON (sub2.value=epm2.plugin_id)
	LEFT JOIN (SELECT value,enabled FROM `{$dbprefix}entity_relationships` b JOIN `{$dbprefix}metadata` c on b.relationship = "active_plugin" AND c.entity_guid = b.guid_one) AS sub3 ON (sub3.value=epm3.plugin_id)
	LEFT JOIN (SELECT value,enabled FROM `{$dbprefix}entity_relationships` b JOIN `{$dbprefix}metadata` c on b.relationship = "active_plugin" AND c.entity_guid = b.guid_one) AS sub4 ON (sub4.value=epm4.plugin_id)
	LEFT JOIN (SELECT value,enabled FROM `{$dbprefix}entity_relationships` b JOIN `{$dbprefix}metadata` c on b.relationship = "active_plugin" AND c.entity_guid = b.guid_one) AS sub5 ON (sub5.value=epm5.plugin_id)

	SET rv.enabled = '$enable_option'
	
	WHERE 
	(
		(epm2.plugin_id = '$plugin') OR
		(epm3.plugin_id = '$plugin') OR
		(epm4.plugin_id = '$plugin') OR
		(epm5.plugin_id = '$plugin')
	) AND (
		((sub2.enabled $enable) OR (epm2.plugin_id IS NULL)) $operator
		((sub3.enabled $enable) OR (epm3.plugin_id IS NULL)) $operator
		((sub4.enabled $enable) OR (epm4.plugin_id IS NULL)) $operator
		((sub5.enabled $enable) OR (epm5.plugin_id IS NULL))
	);
QUERY;
	// error_log($query);
	elgg()->db->updateData($query);
}

?>
