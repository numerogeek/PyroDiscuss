<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

 class Discussion_m extends MY_Model
 {	
	protected $_table = 'discussions';
	
 	public function __construct()
 	{
 		parent::__construct();
 	}
	
	public function get_many_by($params = array())
	{
		$this->db->select('discussions.*, profiles.display_name')
			->join('profiles', 'profiles.user_id = discussions.created_by', 'left');
			
		$this->db->order_by('id', 'desc');
		if (isset($params['limit']) && is_array($params['limit']))
			$this->db->limit($params['limit'][0], $params['limit'][1]);
		elseif (isset($params['limit']))
			$this->db->limit($params['limit']);
			
		$this->db->where('type','topic');

		return $this->get_all();
	
	}

 	public function save_topic($rqstObj)
 	{ 		
 		if ($this->db->table_exists($this->_table))
 		{
 			$hrc = $this->db->insert($this->_table, $rqstObj);

 			if($hrc)
 			{
 				$query = $this->db->get_where(
 					$this->_table, array(
 						'created_by' => $this->current_user->id, 
 						'created_on' => $rqstObj['created_on'],
						'type' 		 => 'topic',
 						)
 					);

 				foreach ($query->result() as $row)
				{
			  	  $topic_id = $row->id;
				}
			
				return $topic_id;
 			}
 		}
		else
		{
			return FALSE;
		}
 	}

	public function get_comments($key) 
	{
		$query = $this->db->get_where($this->_table, array('belongs_to' => $key));
		
		if($query->num_rows() > 0)
		{
			return $query->result();
		}
	
		return FALSE;
	}
 }