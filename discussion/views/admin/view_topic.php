<section class="title">
	<h4><?php echo lang('topic.topic_title_label') ?> &mdash; <?php echo $topic->title; ?></h4>
</section>

<section class="item">

	<div class="row">
		<div class="span2">
			<p><?php echo gravatar($topic->user_email, 70);?></p>
			<div><strong><?php echo $topic->display_name; ?></strong></div>
			<div><?php echo get_group_name($topic->created_by); ?></div>
			<div class="date"> <?php echo format_date($topic->created_on); ?> </div>
		</div>
		<div class="span10">
			<?php echo $topic->parsed; ?>
			<?php if($this->current_user->id == $topic->created_by) { ?>
				<div class="right">
					<?php echo anchor('admin/discussion/edit/' . $topic->id, lang('global:edit'), array('class'=>'btn orange')); ?>
					<?php echo anchor('admin/discussion/delete/' . $topic->id, lang('global:delete'), array('class'=>'confirm btn red delete')); ?>
				</div>
			<?php } ?>
		</div>
		<hr>
	</div>
	

	<?php if($comments) { ?>
	<div class="row">
		<div class="span2">
			<div class="topic_title"><?php echo lang('topic.comments_label'); ?></div>
		</div>
		<div class="span10">
			<?php foreach($comments as $comment): ?>
				<div class="span7"> 
					<?php echo $comment->parsed; ?> 
					<?php if($this->current_user->id == $comment->created_by) { ?>
						<div class="delete_icon">
							<?php echo anchor('admin/discussion/view/'.$topic->id.'/delete/'.$comment->id, lang('global:delete'), 'class="confirm"'); ?>
						</div>
					<?php } ?>
				</div>
				<div class="span2 comment_info">
					<div><?php echo gravatar($comment->user_email, 30);?></div>
					<div>
						<strong><?php echo $comment->display_name; ?></strong><br/>
						<div><?php echo get_group_name($comment->created_by); ?></div><br/>
						<span class="date">	<?php echo format_date($comment->created_on); ?> </span>
					</div>
				</div>
			
			<hr>
			<?php endforeach; ?>
		</div>
	</div>
	<?php } ?>

	<div class="row top10">
		<?php echo form_open('admin/discussion/view/'.$topic->id.'/add', array('class' => 'form-horizontal')); ?>
		<div class="span10 offset2">
			<div class="row">
				<ul>
					<li class="even">
						<label for="add_comment"><?php echo lang('topic.add_comment_label'); ?></label>
						
						<div class="input">
							<br style="clear: both;">
							<?php echo form_textarea(array('id' => 'add_comment', 'name' => 'add_comment', 'value' => $add_comment, 'rows' => 5, 'class'=>'comments wysiwyg-simple') ); ?>
							<button type="submit" class="btn blue"><?php echo lang('topic.submit_button'); ?></button>
						</div>
					</li>
				</ul>
			</div>
			
		</div>
		<?php echo form_close(); ?>
	</div>
	
</section>