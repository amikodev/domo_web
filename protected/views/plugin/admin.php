<?php
/* @var $this PluginController */
/* @var $model Plugin */

$this->breadcrumbs=array(
	'Плагины',
);

$this->menu=array(
	array('label'=>'Добавить плагин', 'url'=>array('create')),
);

?>

<h1>Плагины</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'plugin-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'caption',
		//'actived',
        array(
            'name' => 'actived',
            'value' => '$data->actived?"Да":"Нет"',
        ),
		//'params',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
