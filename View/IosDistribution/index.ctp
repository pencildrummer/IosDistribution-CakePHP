<?php foreach ($builds as $build) : ?>
<p><?php echo $this->Ios->link('Download '.$build['IosBuild']['title'], $build); ?></p>
<?php endforeach; ?>