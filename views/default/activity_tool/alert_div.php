<?php 
$dbprefix = elgg_get_config('dbprefix');
$sql = "SELECT DISTINCT count(*) as `cnt` 
          FROM `{$dbprefix}entities` e 
          LEFT JOIN `{$dbprefix}entity_plugin_mapping` p 
            ON 
            e.`type` = p.`type` AND 
            e.`subtype` = p.`subtype` 
        WHERE e.`type` NOT IN ('site','user') AND 
        e.`subtype` NOT IN ('plugin','elgg_upgrade','widget', 'comment') AND 
        p.`plugin_id` IS NULL";
$dbrow = elgg()->db->getDataRow($sql);
if($dbrow->cnt > 0){
  echo elgg_view_message('error', 
    'There are some subtype that needs to be mapped with a plugn_id. <a href="'.elgg_get_site_url().'admin/administer_utilities/plugin_river_fix">Click here</a> to make the changes.', 
    ['title' => 'Alert!']);
}
 ?>