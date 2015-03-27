<?php
/* @var $this ScenarioController */
/* @var $model Scenario */

$this->breadcrumbs=array(
	'Сценарии'=>array('admin'),
	'Создание',
);

$this->menu=array(
	array('label'=>'Сценарии', 'url'=>array('admin')),
);
?>

<h1>Создание сценария</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>