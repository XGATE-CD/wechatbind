<?php
$this->breadcrumbs=array(
	'Wc Users',
);

$this->menu=array(
	array('label'=>'Create WcUser', 'url'=>array('create')),
	array('label'=>'Manage WcUser', 'url'=>array('admin')),
);
?>

<h1>Wc Users</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
