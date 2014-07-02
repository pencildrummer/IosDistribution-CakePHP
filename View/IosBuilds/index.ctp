<div class="iosBuilds index">
	<h2><?php echo __('Ios Builds'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('title'); ?></th>
			<th><?php echo $this->Paginator->sort('subtitle'); ?></th>
			<th><?php echo $this->Paginator->sort('plist_url'); ?></th>
			<th><?php echo $this->Paginator->sort('ipa_filename'); ?></th>
			<th><?php echo $this->Paginator->sort('bundle_identifier'); ?></th>
			<th><?php echo $this->Paginator->sort('bundle_version'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($iosBuilds as $iosBuild): ?>
	<tr>
		<td><?php echo h($iosBuild['IosBuild']['id']); ?>&nbsp;</td>
		<td><?php echo h($iosBuild['IosBuild']['title']); ?>&nbsp;</td>
		<td><?php echo h($iosBuild['IosBuild']['subtitle']); ?>&nbsp;</td>
		<td><?php echo h($iosBuild['IosBuild']['plist_url']); ?>&nbsp;</td>
		<td><?php echo h($iosBuild['IosBuild']['ipa_filename']); ?>&nbsp;</td>
		<td><?php echo h($iosBuild['IosBuild']['bundle_identifier']); ?>&nbsp;</td>
		<td><?php echo h($iosBuild['IosBuild']['bundle_version']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array(
				'action' => 'view',
				'token' => $iosBuild['IosBuild']['token']
			)); ?>
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
