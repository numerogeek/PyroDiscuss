<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Discussion Module
 *
 * @author		Gogula Krishnan Rajaprabhu
 * @package		PyroCMS\Addon\Modules\Discussion
 * @website		http://netpines.com
 * @version		1.0.2
 */

class Module_Discussion extends Module {

	public $version = '1.0.2';
	public $db_pre;
	
	public function __construct()
	{	
		$this->load->dbforge();
		if(CMS_VERSION >= 1.3) $this->db_pre = SITE_REF.'_';
	}

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Discussions'
			),
			'description' => array(
				'en' => 'A simple discussion panel for members'
			),
			'frontend' => FALSE,
			'backend' => TRUE,
			'menu'		=> 'content',
			'shortcuts' => array(
				array(
			 	   'name' => 'topic.create_button',
				   'uri' => 'admin/discussion/edit',
				   'class' => 'add'
				),
			),
		);
	}

	public function install()
	{
		$this->dbforge->drop_table('discussions');
		
		$sql = "CREATE TABLE IF NOT EXISTS `{$this->db_pre}discussions` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`type` ENUM( 'topic', 'comment' ) collate utf8_unicode_ci NOT NULL DEFAULT 'topic',
				`belongs_to` int(11) NOT NULL default 0,
				`title` varchar(150) collate utf8_unicode_ci NOT NULL default '',
				`desc` text,
				`parsed` text,
				`tot_comments` int(11) NOT NULL default 0,
				`created_on` int(11) NOT NULL default 0,
				`last_updated` int(11) NOT NULL default 0,
				`created_by` int(11) NOT NULL default '0',
				`user_email` varchar(50) collate utf8_unicode_ci NOT NULL default '',
				`display_name` varchar(150) collate utf8_unicode_ci NOT NULL default '',
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";	

		$query = $this->db->query($sql);	
		
		if( !$query ) {
			return FALSE;
		}
		
		return TRUE;	
	}

	public function uninstall()
	{
		$this->dbforge->drop_table('discussions');
		
		return TRUE;
	}

	public function upgrade($old_version)
	{
		return TRUE;
	}

	public function help()
	{
		$help = "<h3>PyroDiscuss v1.0.2</h3>";
		$help .= "PyroDiscuss is a back-end module for PyroCMS and it supports the latest version 2.1.x.<br />"; 
		$help .= "It helps members to start a discussion internally and collaborate provided the group must be give permissions.<br /><br />";
		$help .= "<strong>Features:</strong><br />";
		$help .= "1. create / edit / delete topic<br />";
		$help .= "2. Add / delete comment<br />";
		$help .= "3. View topics<br /><br />";
		$help .= "<strong>Installation:</strong><br />";
		$help .= "1. Download the archive and upload via CP<br />";
		$help .= "2. Install the module<br /><br />";
		$help .= "Reach us for issues / feedback at <a href=\"mailto:hello@netpines.com\"><strong>NetPines Support</strong></a> or tweet us <a href=\"http://twitter.com/netpines\" target=\"_blank\"><strong>@netpines</strong></a><br /><br />";
		$help .= "Note: This is not forum based. A simple discussion panel which is nothing but a single thread in forum.<br />";
		
		return $help;
	}
}
/* End of file details.php */
