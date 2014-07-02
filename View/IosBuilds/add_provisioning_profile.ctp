<div class="iosBuilds form">
<?php echo $this->Form->create(false, array('type' => 'file')); ?>
	<fieldset>
		<legend><?php echo __('Add Ios Build provisioning profile'); ?></legend>
		<p>
			<?php echo __('Upload a compatible profile for bundle identifier <strong>%s</strong>', $iosBuild['IosBuild']['bundle_identifier']); ?>
		</p>
	<?php
		echo $this->Form->hidden('token');
		echo $this->Form->file('provisioning_profile');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Ios Builds'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('Back to build'), array('action' => 'view', 'token' => $iosBuild['IosBuild']['token'])); ?></li>
	</ul>
</div>
