<?php 
run_sql_script(__DIR__ . '/db/activity_tool.sql');
$db = new ElggActivityToolFix();
$db->update_table();
 ?>