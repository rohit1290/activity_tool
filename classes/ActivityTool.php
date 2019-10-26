<?php 
use Elgg\DefaultPluginBootstrap;

class ActivityTool extends DefaultPluginBootstrap {
  
  public function init() {
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
  
  public function activate() {
    $path = dirname(dirname(__FILE__))."/db/activity_tool.sql";
    run_sql_script($path);
    $db = new ElggActivityToolFix();
    $db->update_table();
  }
 
}


 ?>