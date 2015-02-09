<?php
$this->breadcrumbs=array(
	'Wc Users'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List WcUser', 'url'=>array('index')),
	array('label'=>'Create WcUser', 'url'=>array('create')),
	array('label'=>'View WcUser', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage WcUser', 'url'=>array('admin')),
);
?>

<h1>Update WcUser <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>