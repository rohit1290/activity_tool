<?php
$id = get_input('id');

$db = new ElggActivityToolFix();
$db->delete_data($id);

system_message("Entry deleted sucessfully!");

forward(REFERRER);
	?>
