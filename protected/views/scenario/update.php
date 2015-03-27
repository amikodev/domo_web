<?php
/* @var $this ScenarioController */
/* @var $model Scenario */

$this->breadcrumbs=array(
	'Сценарии'=>array('admin'),
	$model->caption=>array('view','id'=>$model->id),
	'Редактирование',
);

$this->menu=array(
	array('label'=>'Сценарии', 'url'=>array('admin')),
	array('label'=>'Создать сценарий', 'url'=>array('create')),
	array('label'=>'Просмотреть', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<h1>Редактирование сценария "<?=CHtml::encode($model->caption)?>"</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>