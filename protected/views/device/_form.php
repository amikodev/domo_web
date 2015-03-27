
<?

$jsParentModels = array();
$parentModels = Device::model()->findAll('type in ('.implode(", ", Yii::app()->params['parentDevices']).')');
foreach($parentModels as $parentModel){
    $childTypes = array();
    foreach($parentModel->childs as $childModel){
        $childTypes[$childModel->type] = null;
    }
    $childTypes = array_keys($childTypes);
    
    $jsParentModels[$parentModel->id] = array(
        'id' => intval($parentModel->id),
        'caption' => $parentModel->caption,
        'type' => intval($parentModel->type),
        //'parentType' => intval($parentModel->parent !== null ? $parentModel->parent->type : 0),
        'childTypes' => $childTypes,
    );
}

?>


<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'device-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'caption'); ?>
		<?php echo $form->textField($model,'caption'); ?>
		<?php echo $form->error($model,'caption'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model,'type', Device::getTypeCaption()); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'parentID'); ?>
		<?php echo $form->dropDownList($model,'parentID', array(''=>'')); ?>
		<?php echo $form->error($model,'parentID'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'connectType'); ?>
		<?php echo $form->dropDownList($model,'connectType', Device::getConnectionCaption()); ?>
		<?php echo $form->error($model,'connectType'); ?>
	</div>

	<div class="row custom pin">
		<?php echo $form->labelEx($model,'pin'); ?>
		<?php echo $form->textField($model,'pin'); ?>
		<?php echo $form->error($model,'pin'); ?>
	</div>

    <!--
	<div class="row custom datechange">
		<?php echo $form->labelEx($model,'datechange'); ?>
		<?php echo $form->textField($model,'datechange'); ?>
		<?php echo $form->error($model,'datechange'); ?>
	</div>

	<div class="row custom value">
		<?php echo $form->labelEx($model,'value'); ?>
		<?php echo $form->textField($model,'value'); ?>
		<?php echo $form->error($model,'value'); ?>
	</div>
    -->
    
	<div class="row custom onewireID">
		<?php echo $form->labelEx($model,'onewireID'); ?>
		<?php echo $form->textField($model,'onewireID',array('size'=>16,'maxlength'=>16)); ?>
		<?php echo $form->error($model,'onewireID'); ?>
	</div>

	<div class="row custom params">
		<?php echo $form->labelEx($model,'params'); ?>
		<?php echo $form->textArea($model,'params',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'params'); ?>
	</div>

    <div id="params-note">
        Пример: 
        <span class="note arduino">{"serial":"/dev/serial/by-id/usb-Arduino__www.arduino.cc__0042_95237323834351419041-if00"}</span>
        <span class="note arduinoradio">{"serial":"/dev/serial/by-id/usb-FTDI_BEEStore_Arduino_NANO_FTXOXVP1-if00-port0", "radio":{"pipe":"F0F0F0F0D2", "pin1":9, "pin2":10}}</span>
        <span class="note boolean">{"valueCaption":{"0":"Включено", "1":"Выключено"}}</span>
        <span class="note shift">{"clockPin":12, "latchPin":8}</span>
    </div>
    
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->



<? Yii::app()->clientScript
        ->registerCss(__FILE__, '

.form .row.custom{
    display: none;
}

#params-note{
    margin-left: 130px;
    display: none;
}
#params-note .note{
    display: none;
    font-family: "Courier New";
    background-color: #EEE;
}

')
        ->registerScript(__FILE__, '

(function(){

var jsParentModels = '.json_encode($jsParentModels).';
var jsConnections = '.json_encode(Device::getConnectionCaption()).';

updateCustomRows();

function updateCustomRows(change){
    var type = parseInt($(".form .row #Device_type").val());
    var parentID = parseInt($(".form .row #Device_parentID").val());
    

    if($.inArray(type, '.json_encode(array_merge(Yii::app()->params['inputDevices'], Yii::app()->params['outputDevices'], Yii::app()->params['1WireDevices'], array(DEV_REGISTR_IN, DEV_REGISTR_OUT))).') != -1){
        $(".form .row.custom.pin").show();
        //$(".form .row.custom.datechange").show();
        //$(".form .row.custom.value").show();
    } else{
        $(".form .row.custom.pin").hide();
        //$(".form .row.custom.datechange").hide();
        //$(".form .row.custom.value").hide();
    }


    if($.inArray(type, '.json_encode(Yii::app()->params['1WireDevices']).') != -1){
        $(".form .row.custom.onewireID").show();
    } else{
        $(".form .row.custom.onewireID").hide();
    }
    

    // do change parentID
    if(typeof change == "undefined" || change == "type"){
        // do change parentID
        $(".form .row #Device_parentID").html("");
        if($.inArray(type, '.json_encode(array(DEV_ARDUINO)).') != -1){
            $(".form .row #Device_parentID").append("<option value=\"\"></option>");
        }
        if($.inArray(type, '.json_encode(array_merge(array(DEV_ARDUINO, DEV_DHT), Yii::app()->params['i2cDevices'], Yii::app()->params['1WireDevices'])).') != -1){
            for(var ind in jsParentModels){
                if(jsParentModels[ind].type == '.DEV_ARDUINO.'){
                    $(".form .row #Device_parentID").append("<option value=\""+jsParentModels[ind].id+"\">"+jsParentModels[ind].caption+"</option>");
                }
            }
        } else if(type == '.DEV_REGISTR_IN.'){
            for(var ind in jsParentModels){
                if($.inArray(jsParentModels[ind].type, '.json_encode(array(DEV_ARDUINO, DEV_REGISTR_IN)).') != -1 && $.inArray('.DEV_REGISTR_IN.', jsParentModels[ind].childTypes) == -1){
                    $(".form .row #Device_parentID").append("<option value=\""+jsParentModels[ind].id+"\">"+jsParentModels[ind].caption+"</option>");
                }
            }
        } else if(type == '.DEV_REGISTR_OUT.'){
            for(var ind in jsParentModels){
                if($.inArray(jsParentModels[ind].type, '.json_encode(array(DEV_ARDUINO, DEV_REGISTR_OUT)).') != -1 && $.inArray('.DEV_REGISTR_OUT.', jsParentModels[ind].childTypes) == -1){
                    $(".form .row #Device_parentID").append("<option value=\""+jsParentModels[ind].id+"\">"+jsParentModels[ind].caption+"</option>");
                }
            }
        } else if($.inArray(type, '.json_encode(Yii::app()->params['inputDevices']).') != -1){
            for(var ind in jsParentModels){
                if($.inArray(jsParentModels[ind].type, '.json_encode(array(DEV_ARDUINO, DEV_REGISTR_IN)).') != -1){
                    $(".form .row #Device_parentID").append("<option value=\""+jsParentModels[ind].id+"\">"+jsParentModels[ind].caption+"</option>");
                }
            }
        } else if($.inArray(type, '.json_encode(Yii::app()->params['outputDevices']).') != -1){
            for(var ind in jsParentModels){
                if($.inArray(jsParentModels[ind].type, '.json_encode(array(DEV_ARDUINO, DEV_REGISTR_OUT)).') != -1){
                    $(".form .row #Device_parentID").append("<option value=\""+jsParentModels[ind].id+"\">"+jsParentModels[ind].caption+"</option>");
                }
            }
        } else{
            for(var ind in jsParentModels){
                $(".form .row #Device_parentID").append("<option value=\""+jsParentModels[ind].id+"\">"+jsParentModels[ind].caption+"</option>");
            }
        }
        
    }
    
    //alert( $(".form .row #Device_parentID option[value=21]").text() );
    
    
    '.(!$model->isNewRecord?'
    $(".form .row #Device_parentID option[value='.intval($model->parentID).']").attr("selected", "selected");
    ':'').'

    // do change connectType
    var parentID = parseInt($(".form .row #Device_parentID").val());
    $(".form .row #Device_connectType").html("");
    if($.inArray(type, '.json_encode(array(DEV_ARDUINO)).') != -1){
        var parentID = parseInt($(".form .row #Device_parentID").val());
        if(parentID){
            $(".form .row #Device_connectType").append("<option value=\"'.CONNECT_RADIO.'\">'.Device::getConnectionCaption(CONNECT_RADIO).'</option>");
        } else{
            $(".form .row #Device_connectType").append("<option value=\"'.CONNECT_USB.'\">'.Device::getConnectionCaption(CONNECT_USB).'</option>");
        }

    } else if($.inArray(type, '.json_encode(Yii::app()->params['1WireDevices']).') != -1){
        $(".form .row #Device_connectType").append("<option value=\"'.CONNECT_ONEWIRE.'\">'.Device::getConnectionCaption(CONNECT_ONEWIRE).'</option>");
    } else if($.inArray(type, '.json_encode(array(DEV_DHT)).') != -1){
        $(".form .row #Device_connectType").append("<option value=\"'.CONNECT_OTHER.'\">'.Device::getConnectionCaption(CONNECT_OTHER).'</option>");
    } else if(parentID && $.inArray(jsParentModels[parentID].type, '.json_encode(Yii::app()->params['1WireDevices']).') != -1){
        $(".form .row #Device_connectType").append("<option value=\"'.CONNECT_PIN.'\">'.Device::getConnectionCaption(CONNECT_PIN).'</option>");
    } else if($.inArray(type, '.json_encode(Yii::app()->params['i2cDevices']).') != -1){
        $(".form .row #Device_connectType").append("<option value=\"'.CONNECT_I2C.'\">'.Device::getConnectionCaption(CONNECT_I2C).'</option>");
    } else{
        $(".form .row #Device_connectType").append("<option value=\"'.CONNECT_PIN.'\">'.Device::getConnectionCaption(CONNECT_PIN).'</option>");
    }


    // do change params
    $("#params-note .note").hide();
    $(".form .row.custom.params").show();
    $("#params-note").show();
    if(type == '.DEV_ARDUINO.'){
        var parentID = parseInt($(".form .row #Device_parentID").val());
        if(parentID){
            $("#params-note .note.arduinoradio").show();
        } else{
            $("#params-note .note.arduino").show();
        }
    } else if($.inArray(type, '.json_encode(Yii::app()->params['booleanDevices']).') != -1){
        $("#params-note .note.boolean").show();
        
    } else if($.inArray(type, '.json_encode(array(DEV_REGISTR_IN, DEV_REGISTR_OUT)).') != -1){
        var parentID = parseInt($(".form .row #Device_parentID").val());
        if(jsParentModels[parentID].type == '.DEV_ARDUINO.'){
            $("#params-note .note.shift").show();
        } else{
            $("#params-note").hide();
            $(".form .row.custom.params").hide();
            $(".form .row.custom.pin").hide();
        }
    } else{
        $(".form .row.custom.params").hide();
        $("#params-note").hide();
    }


}

$(".form .row #Device_type").change(function(){
    updateCustomRows("type");
});
$(".form .row #Device_parentID").change(function(){
    updateCustomRows("parentID");
});


})();

')
        ;
?>
