<div class="iosBuilds form">
<?php echo $this->Form->create('IosBuild', array('type' => 'file')); ?>
	<fieldset>
		<legend><?php echo __('Add Ios Build'); ?></legend>
	<?php
		echo $this->Form->input('title');
		echo $this->Form->input('subtitle');
		echo $this->Form->file('ipa_file');
		echo $this->Form->input('plist_url');
		echo $this->Dropbox->chooser(array(
			'target' => 'IosBuildPlistUrl'
		));
		
		echo $this->Dropbox->scripts(null, array('inline' => false));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Ios Builds'), array('action' => 'index')); ?></li>
	</ul>
</div>
