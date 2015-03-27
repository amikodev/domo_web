<? $model = $sceneDeviceModel->device; ?>
<?
    $controllerModel = null;
    if($model->parent->type == DEV_ARDUINO) $controllerModel = $model->parent;
    else $controllerModel = $model->parent->parent;
?>
<div class="item" data-controller-id="<?=$controllerModel->id?>" data-device-id="<?=$sceneDeviceModel->device->id?>" style="left: <?=$sceneDeviceModel->x?>px; top: <?=$sceneDeviceModel->y?>px; width: <?=$sceneDeviceModel->width?>px; height: <?=$sceneDeviceModel->height?>px;" data-rotate-angle="<?=$sceneDeviceModel->angle?>">
    <div class="background"></div>
    <div class="device type_<?=array_search($model->type, Yii::app()->params['defineDevices'])?>" data-device-type="<?=$model->type?>">
        <? if($model->type == DEV_LED): ?>
        <? elseif($model->type == DEV_BUTTON): ?>
            <div class="image"><?=CHtml::image($model->value?'images/icons/button_off.png':'images/icons/button_on.png')?></div>
        <? elseif($model->type == DEV_TEMPERATURESENSOR): ?>
            <!-- <div class="image"><?=CHtml::image('images/icons/termo.png')?></div> -->
            <div class="value"><span><?=sprintf("%.2f", $model->value)?>&nbsp;</span><sup>&deg;C</sup></div>
            <? if(!$edit): ?><div class="graph"><?=CHtml::link('graph', array('/site/historygraph', 'deviceID'=>$model->id))?></div><? endif; ?>
        <? elseif(in_array($model->type, Yii::app()->params['outputDevices']) && in_array($model->type, Yii::app()->params['booleanDevices'])): ?>
            <div style="text-align: left;">
            DeviceID: <?=$sceneDeviceModel->device->id?> <br/>
            Caption: <?=CHtml::encode($sceneDeviceModel->device->caption)?> <br/>
            <? if(!$edit): ?>
                <? $jsParams = json_decode($model->params, true); ?>
                <div class="value toggle-iphone" data-value-on="<?=$jsParams['valueCaption']['1']=="Включено"?1:0?>" data-value="<?=$model->value?>" style="width: 80px;"><div class="toggle" data-toggle-on="<?=($jsParams['valueCaption']['1']=="Включено" && $model->value) || ($jsParams['valueCaption']['0']=="Включено" && !$model->value) ? "true":"false"?>" data-toggle-width="80"></div></div>
            <? endif; ?>
            </div>
        <? elseif(in_array($model->type, Yii::app()->params['inputDevices']) && in_array($model->type, Yii::app()->params['booleanDevices'])): ?>
            <div style="text-align: left;">
            DeviceID: <?=$sceneDeviceModel->device->id?> <br/>
            Caption: <?=CHtml::encode($sceneDeviceModel->device->caption)?> <br/>
            <? if(!$edit): ?>
                <? $jsParams = json_decode($model->params, true); ?>
                <div class="value" data-value-on="<?=$jsParams['valueCaption']['1']=="Включено"?1:0?>">Value: <span><?=$jsParams['valueCaption'][$model->value]?></span></div> <br/>
            <? endif; ?>
            </div>
        <? else: ?>
            <div style="text-align: left;">
            DeviceID: <?=$sceneDeviceModel->device->id?> <br/>
            Caption: <?=CHtml::encode($sceneDeviceModel->device->caption)?> <br/>
            <div class="value">Value: <span><?=$sceneDeviceModel->device->value?></span></div> <br/>
            <? if(!$edit): ?><div class="graph"><?=CHtml::link('history', array('/site/historydata', 'deviceID'=>$model->id))?></div><? endif; ?>
            </div>
        <? endif; ?>
    </div>
    
    <? if($edit): ?>
    <div class="delete">Delete</div>
    <? endif; ?>
</div>

<? Yii::app()->clientScript
        ->registerScriptFile(Yii::app()->baseUrl.'/js/toggles/toggles.min.js')
        ->registerCssFile(Yii::app()->baseUrl.'/js/toggles/css/toggles-full.css')
        
        //->registerScriptFile(Yii::app()->baseUrl.'/js/iphone-style-checkboxes/jquery/iphone-style-checkboxes.js')
        //->registerCssFile(Yii::app()->baseUrl.'/js/iphone-style-checkboxes/style.css')
        ->registerScript('scene-device-item-script-code', '


')
?>

