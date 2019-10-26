<?php
require_once(dirname(__FILE__) . '/lib/functions.php');

return [
	'bootstrap' => ActivityTool::class,
	'actions' => [
		'activity_tool/save' => ['access'=>'admin'],
		'activity_tool/delete' => ['access'=>'admin'],
	],
];
