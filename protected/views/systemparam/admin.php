<?php
/* @var $this SystemparamController */
/* @var $model SystemParam */

$this->breadcrumbs=array(
	'Системные параметры',
);

$this->menu=array(
	array('label'=>'Создать', 'url'=>array('create')),
);

?>

<h1>Системные параметры</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'system-param-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'caption',
		'value',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
