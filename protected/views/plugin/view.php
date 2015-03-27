<?php
/* @var $this PluginController */
/* @var $model Plugin */

$this->breadcrumbs=array(
	'Плагины'=>array('admin'),
	$model->caption,
);

$this->menu=array(
	array('label'=>'Плагины', 'url'=>array('admin')),
	array('label'=>'Добавить плагин', 'url'=>array('create')),
	array('label'=>'Редактировать', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Вы уверены, что хотите удалить этот элемент?')),
);
?>

<h1>Плагин "<?=CHtml::encode($model->caption)?>"</h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'caption',
        array(
            'name' => 'actived',
            'value' => $model->actived?'Да':'Нет',
        ),
		'params',
	),
)); ?>
