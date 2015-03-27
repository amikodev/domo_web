<?php
/* @var $this ScenarioController */
/* @var $model Scenario */

$this->breadcrumbs=array(
	'Сценарии',
);

$this->menu=array(
	array('label'=>'Создать сценарий', 'url'=>array('create')),
);

?>

<h1>Сценарии</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'scenario-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'caption',
		'delay',
        array(
            'name' => 'deviceID',
            'value' => '$data->device?$data->device->caption:"---"',
        ),
        array(
            'name' => 'actived',
            'value' => '$data->actived?"Да":"Нет"',
        ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
