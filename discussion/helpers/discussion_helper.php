<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Discussion Module
 *
 * @author		Gogula Krishnan Rajaprabhu
 * @package		PyroCMS\Addon\Modules\Discussion\Helpers
 * @website		http://netpines.com
 */

/**
* Return a users group name
*
* @param int $user the users id
* @return  string
*/
function get_group_name($user_id)
{
	if (is_numeric($user_id))
	{
		$user = ci()->ion_auth->get_user($user_id);
		
		$user = (array) $user;
		
		if($user['group_id'])
		{
			$group = ci()->group_m->get_by('id', $user['group_id']);
			
			$group = (array) $group;
			
			return $group['description'];
		
		}
	}
	
	return 'Member';
}
