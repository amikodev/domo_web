<?php
/* @var $this SystemparamController */
/* @var $model SystemParam */

$this->breadcrumbs=array(
	'Системные параметры'=>array('admin'),
	'Создание',
);

$this->menu=array(
	array('label'=>'Системные параметры', 'url'=>array('admin')),
);
?>

<h1>Создание системного параметра</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>