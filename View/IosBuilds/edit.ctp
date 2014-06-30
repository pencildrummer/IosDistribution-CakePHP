<div class="iosBuilds form">
<?php echo $this->Form->create('IosBuild'); ?>
	<fieldset>
		<legend><?php echo __('Edit Ios Build'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('title');
		echo $this->Form->input('subtitle');
		echo $this->Form->input('plist_url');
		echo $this->Form->input('ipa_filename');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('IosBuild.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('IosBuild.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Ios Builds'), array('action' => 'index')); ?></li>
	</ul>
</div>
