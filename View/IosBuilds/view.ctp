<div class="iosBuilds view">
<h2><?php echo __('Ios Build'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($iosBuild['IosBuild']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Title'); ?></dt>
		<dd>
			<?php echo h($iosBuild['IosBuild']['title']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Subtitle'); ?></dt>
		<dd>
			<?php echo h($iosBuild['IosBuild']['subtitle']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Plist Url'); ?></dt>
		<dd>
			<?php echo h($iosBuild['IosBuild']['plist_url']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ipa Filename'); ?></dt>
		<dd>
			<?php echo h($iosBuild['IosBuild']['ipa_filename']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Bundle Identifier'); ?></dt>
		<dd>
			<?php echo h($iosBuild['IosBuild']['bundle_identifier']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Bundle Version'); ?></dt>
		<dd>
			<?php echo h($iosBuild['IosBuild']['bundle_version']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Ios Build'), array('action' => 'edit', $iosBuild['IosBuild']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Ios Build'), array('action' => 'delete', $iosBuild['IosBuild']['id']), array(), __('Are you sure you want to delete # %s?', $iosBuild['IosBuild']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Ios Builds'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Ios Build'), array('action' => 'add')); ?> </li>
	</ul>
</div>
