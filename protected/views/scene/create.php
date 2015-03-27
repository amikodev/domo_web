<?php
/* @var $this SceneController */
/* @var $model Scene */

$this->breadcrumbs=array(
	'Сцены'=>array('admin'),
	'Создание',
);

$this->menu=array(
	array('label'=>'Сцены', 'url'=>array('admin')),
);
?>

<h1>Создание сцены</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>