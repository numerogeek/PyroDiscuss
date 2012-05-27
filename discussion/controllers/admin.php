<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Discussion Module
 *
 * @author		Gogula Krishnan Rajaprabhu
 * @package		PyroCMS\Addon\Modules\Controllers
 * @website		http://netpines.com
 */

 class Admin extends Admin_Controller  
 {
 	protected $create_topic_rules = array(
		'title' => array(
			'field' => 'title',
			'label' => 'lang:topic.title_error',
			'rules' => 'trim|htmlspecialchars|required|max_length[100]'
		),
		'desc' => array(
			'field' => 'desc',
			'label' => 'lang:topic.desc_error',
			'rules' => 'trim|required'
		)
	);
	
	protected $add_comment_rules = array(
		'add_comment' => array(
			'field' => 'add_comment',
			'label' => 'lang:topic.add_comment_error',
			'rules' => 'trim|required|max_length[450]'
		)
	);

 	public function __construct()
 	{
		parent::__construct();
		
		$this->lang->load('discussion');
		$this->load->library('form_validation');
		$this->load->model('discussion_m');
		$this->load->model('groups/group_m');
		$this->load->helper('discussion');

 	}
	
	/**
	* Get the list of topics
	* @access public
	* @return void
	*/
 	public function index() 
 	{
		// should get by topic alone as we use only one table where comments also saved
		$base_where = array('type' => 'topic');
		
		// as usual pagination stuff
		$pagination = create_pagination('admin/discussion/index', $this->discussion_m->count_by($base_where));
		
		$topics = $this->discussion_m->limit($pagination['limit'])->get_many_by();
		
 		$this->template
			->title($this->module_details['name'])
			->append_css('module::discussion.css')
			->set('topics', $topics)
			->set('pagination', $pagination)
 			->build('admin/list_topics');
 	}
	
	/**
	* Create or edit topic
	* @param int $id the topic id
	* @access public
	* @return void
	*/
 	public function edit($id = 0)
 	{
 		$created_now = now();

 		$this->form_validation->set_rules($this->create_topic_rules);
		
		// someone is trying to add a topic
		if($id == 0) 
		{
			if ($this->form_validation->run()) 
			{
				$rqstObj = array(
					'type'				=> 'topic',
					'belongs_to'		=>	0,
					'title'				=> $this->input->post('title'),
					'desc'				=> $this->input->post('desc'),
					'parsed'			=> parse_markdown($this->input->post('desc')),
					'created_on'		=> $created_now,
					'last_updated'		=> $created_now,
					'created_by'		=> $this->current_user->id,
					'user_email'		=> $this->current_user->email,
					'display_name'		=> $this->current_user->display_name,
					);

				// save it in DB.
				$topic_id = $this->discussion_m->save_topic($rqstObj);
		
				if($topic_id) 
				{
					// its inserted in DB and got a valid id. lets redirect it to view page
					$this->session->set_flashdata('success', $this->lang->line('topic.topic_create_success'));

					redirect('admin/discussion/view/'.$topic_id);
				} 
				else 
				{	
					// any problem, display it
					$this->session->set_flashdata('error', $this->lang->line('topic.topic_create_error'));	
				}
			}	
			else 
			{
				// validation fails. get the fields and populate it again.
				foreach ($this->create_topic_rules as $key => $field) 
				{
					$topic->$field['field'] = set_value($field['field']);
				}
			}
		}
		// so he is editing a topic created by him
		else
		{			
			$topic = $this->db->get_where('discussions', array('id' => $id, 'type' => 'topic'))->first_row();
			
			if(!$topic OR $this->current_user->id != $topic->created_by)
			{
				// prevent direct access via URL. only HE is authorized to edit the topic.
				redirect('admin/discussion');
			}

			if ($this->form_validation->run()) 
			{
				$rqstObj = array(
					'title'				=> $this->input->post('title'),
					'desc'				=> $this->input->post('desc'),
					'parsed'			=> parse_markdown($this->input->post('desc')),
					'last_updated'		=> $created_now,
					);
					
				// update the needed info alone.
				$this->db->where('id', $id);
				
				$topic_id = $this->db->update('discussions', $rqstObj);
				
				if($topic_id) 
				{
					// update success. show the view page
					$this->session->set_flashdata('success', $this->lang->line('topic.topic_edit_success'));

					redirect('admin/discussion/view/'.$id);

				} else 
				{	
					// any problem. display it.
					$this->session->set_flashdata('error', $this->lang->line('topic.topic_edit_error'));	
				}
			}
			else
			{
				// get the info for the view page if it is not a POST
				$topic = $this->db->get_where('discussions', array('id' => $id, 'type' => 'topic'))->first_row();
			}
		}

 		$this->template
			->title($this->module_details['name'], sprintf(lang('topic.create_label')))
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
			->append_css('module::discussion.css')
 			->set('topic', $topic)
 			->build('admin/create_topic');
 	
 	}

	/**
	* View topic
	* @access public
	* @param int $topic_id the topic id
	* @param var $option options for comments - add, delete
	* @param int $id the comment id
	* @return void
	*/
 	public function view($topic_id = 0, $option = NULL, $id = 0)
 	{
		$created_now = now();
		
		$add_comment = $this->input->post('add_comment');
		
 		if ( ! $topic_id or ! $topic = $this->db->get_where('discussions', array('id' => $topic_id, 'type' => 'topic'))->first_row() )
		{
			// nothing here. better redirect.
			redirect('admin/discussion');
		}
		
		// add comment
		if($option === 'add')
		{
			$this->form_validation->set_rules($this->add_comment_rules);
			
			if($this->form_validation->run())
			{
				$rqstObj = array(
					'type'				=> 'comment',
					'belongs_to'		=> $topic_id,
					'desc'				=> $this->input->post('add_comment'),
					'parsed'			=> parse_markdown($this->input->post('add_comment')),
					'created_on'		=> $created_now,
					'created_by'		=> $this->current_user->id,
					'user_email'		=> $this->current_user->email,
					'display_name'		=> $this->current_user->display_name,
					);
					
				// insert in the same table with type comment
				$comment_id = $this->db->insert('discussions', $rqstObj);
				
				if($comment_id) 
				{
					// go and update the main record
					$this->db->where('id', $topic_id);
					
					$update = $this->db->update('discussions', array('last_updated' => $created_now, 'tot_comments' => $topic->tot_comments + 1));
				
					$this->session->set_flashdata('success', $this->lang->line('topic.comment_success'));
					
					redirect('admin/discussion/view/'.$topic_id);
				} 
				else 
				{
					// not OK. display error.
					$this->session->set_flashdata('error', $this->lang->line('topic.comment_error'));
				}
			}
			else 
			{
				// validation fails. get the fields and populate it again.
				foreach ($this->add_comment_rules as $key => $field) 
				{
					$field['field'] = set_value($field['field']);
				}
			}
		}
		// deleting a comment
		else if($option === 'delete')
		{
			$query = $this->discussion_m->get_where('discussions', array('id' => $id, 'belongs_to' => $topic_id))->first_row();
			
			if(!$query OR $this->current_user->id != $query->created_by) 
			{
				// prevent direct access via URL. only HE is authorized to delete the comment.
				redirect('admin/discussion');
			}

			$hrc = $this->db->delete('discussions', array('belongs_to' => $topic_id, 'id' => $id));
			
			if($hrc)
			{
				$this->session->set_flashdata('success', $this->lang->line('topic.comment_delete_success'));
			}
			else
			{
				$this->session->set_flashdata('error', $this->lang->line('topic.comment_delete_success'));
			}
			
			redirect('admin/discussion/view/'.$topic_id);
		}
		
		// get the comments for the view page
		$comments = $this->discussion_m->get_comments($topic_id);
			
 		$this->template
			->title($this->module_details['name'], $topic->title)
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
			->append_css('module::discussion.css')
			->set('topic', $topic)
			->set('add_comment', $add_comment)
			->set('comments',$comments)
 			->build('admin/view_topic');
 	} 
	
	/**
	* Delete topic
	* @access public
	* @param int $id the topic id
	* @return void
	*/
	public function delete($id)
	{
		$query = $this->discussion_m->get_where('discussions', array('id' => $id))->first_row();
			
		if(!$query OR $this->current_user->id != $query->created_by) 
		{
			// prevent direct access via URL. only HE is authorized to delete the topic.
			redirect('admin/discussion');
		}

		$hrc1 = $this->db->delete('discussions', array('id' => $id));
		
		$hrc2 = $this->db->delete('discussions', array('belongs_to' => $id));
			
		if($hrc1 AND $hrc2)
		{
			$this->session->set_flashdata('success', $this->lang->line('topic.topic_delete_success'));
				
			redirect('admin/discussion');
		}
		else
		{
			$this->session->set_flashdata('error', $this->lang->line('topic.topic_delete_success'));
			
			$this->view($id);
			
		}
	}
 }