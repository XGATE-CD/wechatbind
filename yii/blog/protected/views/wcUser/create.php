<?php
$this->breadcrumbs=array(
	'Wc Users'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List WcUser', 'url'=>array('index')),
	array('label'=>'Manage WcUser', 'url'=>array('admin')),
);
?>

<h1>Create WcUser</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>