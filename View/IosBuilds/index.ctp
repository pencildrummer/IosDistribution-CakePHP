<div class="iosBuilds index">
	<h2><?php echo __('Ios Builds'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('title'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($iosBuilds as $iosBuild): ?>
	<tr>
		<td>
			<h3><?php echo h($iosBuild['IosBuild']['title']);?> <small><?php echo h($iosBuild['IosBuild']['bundle_version']) . ' (' . h($iosBuild['IosBuild']['build_number']) . ')'; ?></small></h3>
			<small><?php echo h($iosBuild['IosBuild']['bundle_identifier']); ?> &bullet; <?php echo h($iosBuild['IosBuild']['ipa_filename']); ?></small>
			<p><?php echo h($iosBuild['IosBuild']['subtitle']); ?></p>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array(
				'plugin' => 'ios_distribution',
				'controller' => 'ios_builds',
				'action' => 'view',
				'token' => $iosBuild['IosBuild']['token']
			)); ?>
			<?php echo $this->Ios->link(__('Download'), $iosBuild); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $iosBuild['IosBuild']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $iosBuild['IosBuild']['id']), array(), __('Are you sure you want to delete # %s?', $iosBuild['IosBuild']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Ios Build'), array('action' => 'add')); ?></li>
	</ul>
</div>
