<?php
$this->breadcrumbs=array(
	'Wc Users'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List WcUser', 'url'=>array('index')),
	array('label'=>'Create WcUser', 'url'=>array('create')),
	array('label'=>'Update WcUser', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete WcUser', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage WcUser', 'url'=>array('admin')),
);
?>

<h1>View WcUser #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'mobile',
	),
)); ?>
