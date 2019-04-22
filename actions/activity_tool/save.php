<?php 
$type = get_input('type');
$subtype = get_input('subtype');
$plugin_id = get_input('plugin_id');
$db = new ElggActivityToolFix();
for ($i=0; $i < count($type); $i++) {
  $db->insert_data($type[$i], $subtype[$i], $plugin_id[$i]);
}

system_message("Entry saved sucessfully!");

forward(REFERRER);
 ?>