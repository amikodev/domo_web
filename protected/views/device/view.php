<?php
/* @var $this DeviceController */
/* @var $model Device */

$this->breadcrumbs=array(
	'Устройства'=>array('admin'),
	CHtml::encode($model->caption),
);

$this->menu=array(
	array('label'=>'Создать', 'url'=>array('create')),
	array('label'=>'Редактировать', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Удалить', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Вы уверены, что хотите удалить это устройство?')),
);
?>

<h1><?=CHtml::encode($model->caption)?></h1>

<?

$attributes = array();

$attributes[] = 'id';
$attributes[] = 'caption';
$attributes[] = array(
    'name' => 'type',
    'value' => Device::getTypeCaption($model->type),
);

if($model->parent !== null){
    $attributes[] = array(
        'name' => 'parentID',
        'type' => 'raw',
        'value' => CHtml::link(CHtml::encode($model->parent->caption), array('/device/view', 'id'=>$model->parentID)),
    );
}

$attributes[] = array(
    'name' => 'connectType',
    'value' => Device::getConnectionCaption($model->connectType),
);

if(in_array($model->type, array_merge(Yii::app()->params['inputDevices'], Yii::app()->params['outputDevices'], Yii::app()->params['1WireDevices']))){
    $attributes[] = 'pin';
    $attributes[] = 'datechange';
    $attributes[] = 'value';
}

if(in_array($model->type, Yii::app()->params['1WireDevices'])){
    $attributes[] = 'onewireID';
}

$attributes[] = array(
    'name' => 'params',
    'type' => 'raw',
    'value' => $this->htmlFormatParams(json_decode($model->params, true)),
);

?>


<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>$attributes,
)); ?>



<?




?>

<? if(sizeof($model->childs) > 0): ?>

<br/><br/>
<h3>Дочерние элементы</h3>

<ul class="device-childs">
<? foreach($model->childs as $childModel): ?>
    <li>
        <?=CHtml::link(CHtml::encode("{$childModel->caption} [{$childModel->id}]"), '#')?>
        <div class="view"><? $this->renderPartial('application.views.device.view', array('model'=>$childModel)); ?></div>
    </li>
<? endforeach; ?>
</ul>

<? endif; ?>


<? Yii::app()->clientScript
        ->registerCss(__FILE__, '

.device-childs li .view{
    display: none;
    zoom: 0.9;
}

')
        ->registerScript(__FILE__, '

$(".device-childs li a").click(function(){
    $(this).closest("li").find(".view").toggle();
    return false;
});

')
        ;
?>


