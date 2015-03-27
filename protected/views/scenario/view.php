<?php
/* @var $this ScenarioController */
/* @var $model Scenario */

$this->breadcrumbs=array(
	'Сценарии'=>array('admin'),
	$model->caption,
);

$this->menu=array(
	array('label'=>'Сценарии', 'url'=>array('admin')),
	array('label'=>'Создать сценарий', 'url'=>array('create')),
	array('label'=>'Редактировать', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Вы уверены, что хотите удалить этот элемент?')),
);
?>

<h1>Сценарий "<?=CHtml::encode($model->caption)?>"</h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'caption',
		'delay',
		//'deviceID',
		//'scenarioComponent',
        array(
            'name' => 'deviceID',
            'value' => CHtml::encode($model->device->caption),
        ),
        array(
            'name' => 'actived',
            'value' => $model->actived?'Да':'Нет',
        ),
		//'content',
	),
)); ?>

<div id="editor"><?=CHtml::encode($model->content)?></div>

<? Yii::app()->clientScript
        ->registerScriptFile(Yii::app()->baseUrl.'/js/ace/src-min-noconflict/ace.js')
        ->registerCss('scenario-style-code', '

#editor { 
    position: relative;
    width: 100%;
    height: 300px;
}

#editor .ace_gutter{
    display: none;
}


')
        ->registerScript('scenario-script-code', '


var editor = ace.edit("editor");
editor.setTheme("ace/theme/tomorrow");
editor.getSession().setMode("ace/mode/php");
editor.setReadOnly(true);


')
?>



