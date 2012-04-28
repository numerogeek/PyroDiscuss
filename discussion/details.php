<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * Discussion Module
 *
 * @author		Gogula Krishnan Rajaprabhu
 * @package		Netpines
 * @subpackage	Discussion Module
 * @category	Modules
 * @website		http://netpines.com
 */

class Module_Discussion extends Module {

	public $version = '0.9.0';
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
				'en' => 'Discussion'
			),
			'description' => array(
				'en' => 'A simple discussion panel for members'
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu'		=> 'content',
			'shortcuts' => array(
				array(
			 	   'name' => 'topic.create_button',
				   'uri' => 'admin/discussion/create',
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
				`desc` varchar(750) collate utf8_unicode_ci NOT NULL default '',
				`comment` varchar(250) collate utf8_unicode_ci NOT NULL default '',
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
		return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
	}
}
/* End of file details.php */
