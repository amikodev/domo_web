<?php
/* @var $this PluginController */
/* @var $model Plugin */

$this->breadcrumbs=array(
	'Плагины'=>array('admin'),
	'Добавление',
);

$this->menu=array(
	array('label'=>'Плагины', 'url'=>array('admin')),
);
?>

<h1>Добавление плагина</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>