<?php
class ElggActivityToolFix {
	
	private $dbprefix;
	
	function __construct() {
		$this->dbprefix = elgg_get_config('dbprefix');
	}
	
	public function update_table() {
		$entity_query = elgg()->db->getData("SELECT DISTINCT e.`type`,e.`subtype`,m.`plugin_id` 
				FROM `{$this->dbprefix}entities` e, 
				(SELECT m.`value` as `plugin_id` 
					FROM `{$this->dbprefix}entities` e,`{$this->dbprefix}metadata` m 
					WHERE e.`subtype`='plugin' AND e.`guid`= m.`entity_guid`) m 
				WHERE `type` NOT IN ('site','user') AND
				 `subtype` NOT IN ('plugin','elgg_upgrade','widget', 'comment') AND
				 e.`subtype`=m.`plugin_id`");

		foreach ($entity_query as $row) {
			$this->insert_data($row->type, $row->subtype, $row->plugin_id, false);
		}
	}
	
	public function insert_data($type, $subtype, $plugin_id, $user_invoked = true) {
		$dbrow = elgg()->db->getDataRow("SELECT `id` FROM `{$this->dbprefix}entity_plugin_mapping` WHERE `type` = '$type' AND `subtype` = '$subtype' LIMIT 1");
		if($dbrow->id == null){
			// Insert DB ROW
			elgg()->db->insertData("INSERT INTO `{$this->dbprefix}entity_plugin_mapping` (`type`, `subtype`, `plugin_id`) VALUES ('$type', '$subtype', '$plugin_id')");
		} else {
			// Update DB ROW
			if($user_invoked){
				elgg()->db->updateData("UPDATE `{$this->dbprefix}entity_plugin_mapping` SET `plugin_id`='$plugin_id' WHERE `id` = '$dbrow->id'");
			}
		}
	}

	public function delete_data($id) {
		elgg()->db->deleteData("DELETE FROM `{$this->dbprefix}entity_plugin_mapping` WHERE `id` = '$id'");
	}
}
