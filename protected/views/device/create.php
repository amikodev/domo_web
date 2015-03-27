<?php
/* @var $this DeviceController */
/* @var $model Device */

$this->breadcrumbs=array(
	'Устройства'=>array('admin'),
    'Создание',
);

$this->menu=array(
);
?>

<h1>Создание устройства</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>