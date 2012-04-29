<section class="title">
	<h4><?php echo lang('topic.topic_all_label'); ?></h4>
</section>

<section class="item">
	<?php if ($topics): ?>
	<table class="table table-striped">
		<thead>
		  <tr>
			<th><?php echo lang('topic.topic_title_label'); ?></th>
			<th class="collapse"><?php echo lang('topic.topic_author_label'); ?></th>
			<th class="collapse"><?php echo lang('topic.topic_created_label'); ?></th>
			<th class="collapse"><?php echo lang('topic.comments_label'); ?></th>
			<th class="collapse"><?php echo lang('topic.topic_updated_label'); ?></th>
			<th width="120"></th>
		  </tr>
		</thead>
		<tbody>
			<?php foreach($topics as $topic): ?>
			<tr>
				<td><?php echo $topic->title; ?></td>
				<td><?php echo anchor('user/' . $topic->created_by, $topic->display_name, 'target="_blank"'); ?></td>
				<td><?php echo format_date($topic->created_on); ?></td>
				<td style="text-align:center"><?php echo $topic->tot_comments; ?></td>
				<td>
					<?php 
						if(floor(now() - $topic->created_on/604800) > 0) { 
							echo timespan($topic->created_on)." ago";
						} else { 
							echo format_date($topic->created_on);
						} 
					?> 
				</td>
				<td>
					<?php echo anchor('admin/discussion/view/' . $topic->id, lang('global:view'), array('class'=>'btn green')); ?>
					<?php echo anchor('admin/discussion/delete/' . $topic->id, lang('global:delete'), array('class'=>'confirm btn red delete')); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>	
	<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
	
	<?php else: ?>
		<div class="no_data"><?php echo lang('topic.no_topics');?></div>
	<?php endif; ?>
</section>
