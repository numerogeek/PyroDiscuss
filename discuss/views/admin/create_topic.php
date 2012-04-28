<section class="title">
	<h4><?php echo lang('topic.create_label'); ?></h4>
</section>

<section class="item">
	<?php echo form_open(uri_string(), array('class' => 'form-horizontal')); ?>
		<div class="form_inputs">
			<ul>
				<li class="even">
					<label for="name"><?php echo lang('topic.title_label'); ?> <span>*</span></label>
					<div class="input"><?php echo form_input('title', htmlspecialchars_decode($topic->title), 'style="width:98%"'); ?></div>
				</li>
				
				<li class="">
					<label for="data"><?php echo lang('topic.desc_label'); ?> <span>*</span></label>
					<div class="input"><?php echo form_textarea(array('id' => 'desc', 'name' => 'desc', 'value' => $topic->desc, 'rows' => 10) ); ?></div>
				</li>
			</ul>
			<div><?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?></div>
		</div>
	<?php echo form_close(); ?>
</section>