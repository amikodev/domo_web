<?php
/* @var $this DeviceController */
/* @var $model Device */

$this->breadcrumbs=array(
	'Устройства',
);

$this->menu=array(
	array('label'=>'Создать', 'url'=>array('create')),
);

?>

<h1>Устройства</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'device-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'caption',
        
        array(
            'name' => 'type',
            'value' => 'Device::getTypeCaption($data->type)',
            'filter' => Device::getTypeCaption(),
        ),
        
		'pin',
        
        array(
            'name' => 'datechange',
            'filter' => '',
        ),
        array(
            'name' => 'value',
            'filter' => '',
        ),
		
        array(
            'name' => 'parentID',
            'value' => '$data->parent ? $data->parent->caption : ""',
        ),
        
        array(
            'name' => 'connectType',
            'value' => 'Device::getConnectionCaption($data->connectType)',
            'filter' => Device::getConnectionCaption(),
        ),
        
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
