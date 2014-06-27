<?php foreach ($builds as $build) : ?>
<?php echo $this->Ios->link('Download '.$build['IosBuild']['title'], $build); ?>
<?php endforeach; ?>