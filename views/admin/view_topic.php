<section class="title">
	<h4><?php echo lang('topic.topic_title_label') ?></h4>
</section>

<section class="item">

	<div class="row">
		<div class="span2">
			<p><?php echo gravatar($topic->user_email, 70);?></p>
			<div><?php echo $topic->display_name; ?></div>
			<div><?php echo format_date($topic->created_on); ?></div>
		</div>
		<div class="span10">
			<div class="topic_title"><?php echo $topic->title; ?></div>
			<?php echo $topic->desc; ?>
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
					<?php echo $comment->comment; 
					if($enable_delete) { ?>
						<div class="top10 icon-remove"><?php echo anchor('admin/discussion/view/'.$topic->id.'/delete/'.$comment->id, lang('global:delete'), 'class="pad15"'); ?></div>
					<?php } ?>
				</div>
				<div class="span2 comment_info">
					<div><?php echo gravatar($comment->user_email, 30);?></div>
					<div>
						<?php echo $comment->display_name; ?><br/>
						<span class="date"><?php echo format_date($comment->created_on); ?></span>
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
			<div class="span2">
				<h5><?php echo lang('topic.add_comment_label'); ?> </h5>
			</div>
			<div class="span7">
				<div>
					<?php echo form_textarea(array('id' => 'add_comment', 'name' => 'add_comment', 'value' => $add_comment, 'rows' => 5, 'class'=>'comments wysiwyg-simple') ); ?>
					<button type="submit" class="btn blue"><?php echo lang('topic.submit_button'); ?></button>
				</div>
			</div>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
	
</section>