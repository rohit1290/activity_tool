<?php 
echo elgg_vieW_field([
			'#type' => 'select',
			'#label' => elgg_echo('Enable/Disable Activity on User Ban?'),
			'name' => 'params[activity_on_user_ban]',
			'value' => $vars['entity']->activity_on_user_ban,
			'options' => [
        'yes' => 'Yes',
        'no' => 'No'
      ],
		]);
 ?>