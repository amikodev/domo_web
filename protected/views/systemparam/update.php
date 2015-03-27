<?php
/* @var $this SystemparamController */
/* @var $model SystemParam */

$this->breadcrumbs=array(
	'Системные параметры'=>array('admin'),
	CHtml::encode($model->name),
	'Редактирование',
);

$this->menu=array(
	array('label'=>'Создать', 'url'=>array('create')),
	array('label'=>'Системные параметры', 'url'=>array('admin')),
);
?>

<h1>Системный параметр "<?=CHtml::encode($model->name)?>"</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>