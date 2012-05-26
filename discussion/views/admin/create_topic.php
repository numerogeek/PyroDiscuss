<section class="title">
	<h4><?php if($topic->title == "") { echo lang('topic.create_label'); } else { echo lang('topic.edit_label'); } ?></h4>
</section>

<section class="item">
	<?php echo form_open(uri_string(), array('class' => 'form-horizontal')); ?>
		<div class="form_inputs">
			<ul>
				<li class="even">
					<label for="title">
						<?php echo lang('topic.title_label'); ?> <span>*</span>
						<small><?php echo lang('topic.title_helper_label'); ?></small>
					</label>
					<div class="input"><?php echo form_input('title', htmlspecialchars_decode($topic->title), 'maxlength="100" style="width:98%"'); ?></div>
				</li>
				
				<li class="">
					<label for="desc">
						<?php echo lang('topic.desc_label'); ?> <span>*</span>
						<small><?php echo lang('topic.desc_helper_label'); ?></small>
					</label>
					<div class="input"><?php echo form_textarea(array('id' => 'desc', 'name' => 'desc', 'value' => $topic->desc, 'rows' => 40, 'class' => 'wysiwyg-simple') ); ?></div>
				</li>
			</ul>
			<div><?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?></div>
		</div>
	<?php echo form_close(); ?>
</section>