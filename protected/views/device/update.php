<?php
/* @var $this DeviceController */
/* @var $model Device */

$this->breadcrumbs=array(
	'Устройства'=>array('admin'),
	CHtml::encode($model->caption)=>array('view', 'id'=>$model->id),
    'Редактирование',
);

$this->menu=array(
	array('label'=>'Создать', 'url'=>array('create')),
	array('label'=>'Просмотреть', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<h1>Редактирование устройства "<?=CHtml::encode($model->caption)?>"</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>