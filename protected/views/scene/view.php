<?php
/* @var $this SceneController */
/* @var $model Scene */

$this->breadcrumbs=array(
	'Сцены'=>array('admin'),
	$model->caption,
);

$this->menu=array(
	array('label'=>'Сцены', 'url'=>array('admin')),
	array('label'=>'Создать сцену', 'url'=>array('create')),
	array('label'=>'Редактировать', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Вы уверены, что хотите удалить этот элемент?')),
);
?>

<h1>Сцена "<?=CHtml::encode($model->caption)?>"</h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'caption',
	),
)); ?>

<? if($model->image): ?>
<br/><br/>
<div style="overflow: auto;">
    <?=CHtml::image(Yii::app()->baseUrl.'/images/scenes/'.$model->image, '', array())?>
</div>
<? endif; ?>

