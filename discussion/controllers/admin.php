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

 	}

 	public function index() 
 	{
		$pagination = create_pagination('admin/discussion/index', $this->discussion_m->count_all(), NULL, 3);
		
		$topics = $this->discussion_m->limit($pagination['limit'])->get_many_by();
		
 		$this->template
			->title($this->module_details['name'])
			->append_css('module::discussion.css')
			->set('topics', $topics)
			->set('pagination', $pagination)
 			->build('admin/list_topics');
 	}

 	public function edit($id = 0)
 	{
 		$created_now = now();

 		$this->form_validation->set_rules($this->create_topic_rules);
		
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

				$topic_id = $this->discussion_m->save_topic($rqstObj);
		
				if($topic_id) 
				{
					$this->session->set_flashdata('success', $this->lang->line('topic.topic_success'));

					redirect('admin/discussion/view/'.$topic_id);

				} else 
				{	
					$this->session->set_flashdata('error', $this->lang->line('topic.topic_error'));	
				}
			}	
			else 
			{
				foreach ($this->create_topic_rules as $key => $field) 
				{
					$topic->$field['field'] = set_value($field['field']);
				}
			}
		}
		else
		{			
			if ($this->form_validation->run()) 
			{
				$rqstObj = array(
					'title'				=> $this->input->post('title'),
					'desc'				=> $this->input->post('desc'),
					'parsed'			=> parse_markdown($this->input->post('desc')),
					'last_updated'		=> $created_now,
					);
					
				$this->db->where('id', $id);
				
				$topic_id = $this->db->update('discussions', $rqstObj);
				
				if($topic_id) 
				{
					$this->session->set_flashdata('success', $this->lang->line('topic.topic_success'));

					redirect('admin/discussion/view/'.$topic_id);

				} else 
				{	
					$this->session->set_flashdata('error', $this->lang->line('topic.topic_error'));	
				}
			}
			else
			{
				$topic = $this->db->get_where('discussions', array('id' => $id, 'type' => 'topic'))->first_row();
			}
		}

 		$this->template
			->title($this->module_details['name'], sprintf(lang('topic.create_label')))
			->append_css('module::discussion.css')
 			->set('topic', $topic)
 			->build('admin/create_topic');
 	
 	}

 	public function view($topic_id = 0, $option = NULL, $id = 0)
 	{
		$created_now = now();
		
		$enable_delete = FALSE;
		
		$add_comment = $this->input->post('add_comment');
		
 		if ( ! $topic_id or ! $topic = $this->db->get_where('discussions', array('id' => $topic_id, 'type' => 'topic'))->first_row() )
		{
			redirect('admin/discussion');
		}
		
		if($option === 'add')
		{
			$this->form_validation->set_rules($this->add_comment_rules);
			
			if($this->form_validation->run())
			{
				$rqstObj = array(
					'type'				=> 'comment',
					'belongs_to'		=> $topic_id,
					'comment'			=> parse_markdown(htmlspecialchars($this->input->post('add_comment'), NULL, FALSE)),
					'created_on'		=> $created_now,
					'created_by'		=> $this->current_user->id,
					'user_email'		=> $this->current_user->email,
					'display_name'		=> $this->current_user->display_name,
					);
					
				$comment_id = $this->db->insert('discussions', $rqstObj);
				
				if($comment_id) 
				{
					$this->db->where('id', $topic_id);
					
					$update = $this->db->update('discussions', array('last_updated' => $created_now, 'tot_comments' => $topic->tot_comments + 1));
				
					$this->session->set_flashdata('success', $this->lang->line('topic.comment_success'));
					
					redirect('admin/discussion/view/'.$topic_id);

				} 
				else 
				{
					$this->session->set_flashdata('error', $this->lang->line('topic.comment_error'));
					
				}
			}
			else 
			{
				foreach ($this->add_comment_rules as $key => $field) 
				{
					$field['field'] = set_value($field['field']);
				}
			}
		}
		else if($option === 'delete')
		{
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
		
		$comments = $this->discussion_m->get_comments($topic_id);
	
		if($comments)
		{
			foreach($comments as $row)
			{
				if($row->created_by == $this->current_user->id)
				{
					$enable_delete = TRUE;
				}
			}
		}		
			
 		$this->template
			->title($this->module_details['name'], sprintf(lang('topic.topic_title_label'), $topic->title))
			->append_css('module::discussion.css')
			->set('topic', $topic)
			->set('add_comment', $add_comment)
			->set('comments',$comments)
			->set('enable_delete', $enable_delete)
 			->build('admin/view_topic');
 	} 
	
	public function delete($id)
	{
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