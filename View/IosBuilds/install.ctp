<h1><?php echo h(__('Install profile')); ?></h1>

<p>
</p>

<?php echo $this->Ios->profile(__('Install'), $iosBuild, array(
	'id' => 'profile_link'
)); ?>

<?php echo $this->Ios->link(__('Download app'), $iosBuild, array(
	'id' => 'download_link',
	'style' => 'display: none;'
)); ?>

<script type="text/javascript">
document.getElementById('profile_link').onclick = function(e) {
	e.currentTarget.style.display = 'none';
	document.getElementById('download_link').style.display = 'block';
};
</script>