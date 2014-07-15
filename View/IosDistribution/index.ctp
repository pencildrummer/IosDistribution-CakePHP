<?php foreach ($builds as $build) : ?>
<p><?php echo $this->Ios->install('Install '.$build['IosBuild']['title'], $build); ?></p>
<?php endforeach; ?>