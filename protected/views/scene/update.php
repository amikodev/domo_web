<?php
/* @var $this SceneController */
/* @var $model Scene */

$this->breadcrumbs=array(
	'Сцены'=>array('admin'),
	$model->caption=>array('view','id'=>$model->id),
	'Редактирование',
);

$this->menu=array(
	array('label'=>'Сцены', 'url'=>array('admin')),
	array('label'=>'Создать сцену', 'url'=>array('create')),
	array('label'=>'Просмотреть', 'url'=>array('view', 'id'=>$model->id)),
);

$this->layout = 'column1';

?>

<h1>Редактирование сцены "<?=CHtml::encode($model->caption)?>"</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>


<hr/>


<?

$scModel = $model;

$scDevIDs = array();
foreach($scModel->sceneDevices as $sceneDeviceModel){
    $scDevIDs[] = $sceneDeviceModel->device->id;
}

$jsDeviceImages = array();
foreach($deviceImages as $n=>$deviceImage){
    $jsDeviceImages[] = array(
        'text' => $deviceImage,
        'value' => $deviceImage,
        'selected' => 'js:false',
        'description' => '',
        'imageSrc' => Yii::app()->baseUrl.'/images/icons/'.$deviceImage,
    );
}


$jsWidgets = array();
foreach($scModel->widgets as $widgetModel){
    $jsWidgets[$widgetModel->id] = array(
        'id' => $widgetModel->id,
        'caption' => $widgetModel->caption,
        'type' => $widgetModel->type,
        'params' => json_decode($widgetModel->params, true),
    );
}

?>

<div class="span-6">

    <div id="widgetAdd">+</div>
    <div id="widgetIcon"></div>
    <div class="clear"></div>
    
    <div id="widgetParams">
        <div class="form">

        <? /* */ ?>
        <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'widget-params-form',
            'enableAjaxValidation'=>false,
        )); ?>
        <? /* */ ?>
            
            
            <div class="row">
                <?php echo $form->labelEx($sceneWidgetModel,'caption'); ?>
                <?php echo $form->textField($sceneWidgetModel,'caption'); ?>
                <?php echo $form->error($sceneWidgetModel,'caption'); ?>
            </div>
            
            <div class="row">
                <?php echo $form->labelEx($sceneWidgetModel,'type'); ?>
                <?php echo $form->dropDownList($sceneWidgetModel,'type', SceneWidget::getType()); ?>
                <?php echo $form->error($sceneWidgetModel,'type'); ?>
            </div>
            
            <?php //echo $form->hiddenField($sceneWidgetModel,'id'); ?>
            <?php echo $form->hiddenField($sceneWidgetModel,'sceneID'); ?>
            <?=CHtml::hiddenField('widgetID', '')?>
           
            <div style="border-top: 1px #BBB solid;"></div>
            <form></form>
            
            <div id="wp_type_<?=SceneWidget::TYPE_TEXT?>" class="partion">
            <form>
                <div class="row"> <?=CHtml::label('Текст', 'wp_text')?> <?=CHtml::textField('wp_text', '')?> </div>
                <div class="row"> <?=CHtml::label('Размер шрифта', 'wp_fontsize')?> <?=CHtml::textField('wp_fontsize', '')?> </div>
                <div class="row"> <?=CHtml::label('Цвет текста', 'wp_color')?> <?=CHtml::textField('wp_color', '')?> </div>
            </form>
            </div>

            <div id="wp_type_<?=SceneWidget::TYPE_DEVICEVALUE?>" class="partion">
            <form>
                <div class="row"> <?=CHtml::label('Устройство', 'wp_deviceID')?> <?=CHtml::dropDownList('wp_deviceID', '', CHtml::listData($executiveDevices, 'id', 'caption'))?> </div>
                <div class="row"> <?=CHtml::label('Шаблон', 'wp_template')?> <?=CHtml::textField('wp_template', '{$image}{$value}')?> </div>
                <div class="row"> <?=CHtml::label('Изображение', 'wp_image')?> <?=CHtml::dropDownList('wp_image', '', array())?> </div>
                <div class="row"> <?=CHtml::label('Ширина', 'wp_imagewidth')?> <?=CHtml::textField('wp_imagewidth', '')?> </div>
                <div class="row"> <?=CHtml::label('Высота', 'wp_imageheight')?> <?=CHtml::textField('wp_imageheight', '')?> </div>
                <div class="row"> <?=CHtml::label('Размер шрифта', 'wp_valuefontsize')?> <?=CHtml::textField('wp_valuefontsize', '')?> </div>
                <div class="row"> <?=CHtml::label('Цвет текста', 'wp_valuecolor')?> <?=CHtml::textField('wp_valuecolor', '')?> </div>

                <?=CHtml::hiddenField('wp_value', sizeof($executiveDevices)>0?$executiveDevices[0]->value:0)?>
                <?=CHtml::hiddenField('wp_imagevalue', '')?>
            </form>
            </div>

            <div id="wp_type_<?=SceneWidget::TYPE_CHECKBOX?>" class="partion">
            <form>
                <div class="row"> <?=CHtml::label('Устройство', 'wp_deviceID')?> <?=CHtml::dropDownList('wp_deviceID', '', CHtml::listData($executiveDevices, 'id', 'caption'))?> </div>
                <div class="row"> <?=CHtml::label('Изображение 0', 'wp_image0')?> <?=CHtml::dropDownList('wp_image0', '', array())?> </div>
                <div class="row"> <?=CHtml::label('Изображение 1', 'wp_image1')?> <?=CHtml::dropDownList('wp_image1', '', array())?> </div>
                <div class="row"> <?=CHtml::label('Ширина', 'wp_imagewidth')?> <?=CHtml::textField('wp_imagewidth', '')?> </div>
                <div class="row"> <?=CHtml::label('Высота', 'wp_imageheight')?> <?=CHtml::textField('wp_imageheight', '')?> </div>
                <div class="row"> <?=CHtml::label('Интерактивный', 'wp_interact')?> <?=CHtml::dropDownList('wp_interact', '', array('Нет', 'Да'))?> </div>

                <?=CHtml::hiddenField('wp_value', sizeof($executiveDevices)>0?$executiveDevices[0]->value:0)?>
                <?=CHtml::hiddenField('wp_image0value', '')?>
                <?=CHtml::hiddenField('wp_image1value', '')?>
            </form>
            </div>

            <div id="wp_type_<?=SceneWidget::TYPE_VIDEO?>" class="partion">
            <form>
                <div class="row"> <?=CHtml::label('URL', 'wp_url')?> <?=CHtml::textField('wp_url', '')?> </div>
                <div class="row"> <?=CHtml::label('Изображение', 'wp_image')?> <?=CHtml::dropDownList('wp_image', '', array())?> </div>
                <div class="row"> <?=CHtml::label('Трансляция', 'wp_stream')?> <?=CHtml::dropDownList('wp_stream', '', array('Нет', 'Да'))?> </div>
                <div class="row"> <?=CHtml::label('Ширина', 'wp_imagewidth')?> <?=CHtml::textField('wp_imagewidth', '')?> </div>
                <div class="row"> <?=CHtml::label('Высота', 'wp_imageheight')?> <?=CHtml::textField('wp_imageheight', '')?> </div>
            
                <?=CHtml::hiddenField('wp_imagevalue', '')?>
            </form>
            </div>

            <div id="wp_type_<?=SceneWidget::TYPE_DEVICEMULTIVALUE?>" class="partion">
            <form>
                <div class="row"> <?=CHtml::label('Устройство', 'wp_deviceID')?> <?=CHtml::dropDownList('wp_deviceID', '', CHtml::listData($executiveDevices, 'id', 'caption'))?> </div>
                <div class="row"> <?=CHtml::label('Шаблон', 'wp_template')?> <?=CHtml::textField('wp_template', '{$image}{$value}')?> </div>
                <div class="row"> <?=CHtml::label('Изображение', 'wp_image')?> <?=CHtml::dropDownList('wp_image', '', array())?> </div>
                <div class="row"> <?=CHtml::label('Ширина', 'wp_imagewidth')?> <?=CHtml::textField('wp_imagewidth', '')?> </div>
                <div class="row"> <?=CHtml::label('Высота', 'wp_imageheight')?> <?=CHtml::textField('wp_imageheight', '')?> </div>
                <div class="row"> <?=CHtml::label('Размер шрифта', 'wp_valuefontsize')?> <?=CHtml::textField('wp_valuefontsize', '')?> </div>
                <div class="row"> <?=CHtml::label('Цвет текста', 'wp_valuecolor')?> <?=CHtml::textField('wp_valuecolor', '')?> </div>

                <?=CHtml::hiddenField('wp_value', sizeof($executiveDevices)>0?$executiveDevices[0]->value:0)?>
                <?=CHtml::hiddenField('wp_imagevalue', '')?>
            </form>
            </div>

            <div style="border-top: 1px #BBB solid;"></div>

            <div id="wp_widget_css" class="">
            <form>
                <div class="row"> <?=CHtml::label('CSS', 'wp_css')?> <?=CHtml::textArea('wp_css', "border: 1px #DDD solid; \nbackground-color: #EEE; \npadding: 5px;")?> </div>
            </form>
            </div>
            
            <div class="row buttons">
                <?php echo CHtml::submitButton('Сохранить'); ?>
            </div>

        <? /* */ ?>
        <?php $this->endWidget(); ?>
        <? /* */ ?>

        </div><!-- form -->
        
    </div>
    
    
</div>
<div class="span-19">

    <div id="scene_container">
        <div id="scene_image">
            <? if($scModel->image): ?>
            <?=CHtml::image(Yii::app()->baseUrl.'/images/scenes/'.$scModel->image, '', array())?>
            <? endif; ?>
        </div>
        <div id="scene_widgets">
            
        </div>
    </div>

    <br/>
    <div class="clear"></div>

</div>
    
<? Yii::app()->clientScript
        
        ->registerCssFile(Yii::app()->baseUrl.'/js/jquery-ui/themes/base/core.css')
        ->registerCssFile(Yii::app()->baseUrl.'/js/jquery-ui/themes/base/resizable.css')
        
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jquery-ui/core.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jquery-ui/widget.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jquery-ui/mouse.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jquery-ui/draggable.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jquery-ui/resizable.js')
        
        
        ->registerCssFile(Yii::app()->baseUrl.'/js/jquery-ui-rotatable/jquery.ui.rotatable.css')
        //->registerScriptFile(Yii::app()->baseUrl.'/js/jquery-ui-rotatable/jquery.ui.rotatable.min.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jquery-ui-rotatable/jquery.ui.rotatable.js')

        //->registerScriptFile(Yii::app()->baseUrl.'/js/jQueryRotateCompressed.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/ddslick/jquery.ddslick.min.js')

        ->registerScriptFile(Yii::app()->baseUrl.'/js/DomoWidget.js')
        
        ->registerCss(__FILE__, '
            

.ui-wrapper {
	overflow:   visible !important;}
	
.ui-resizable-handle {
	background:    #f5dc58;
	border:        1px solid #FFF;
	
	z-index:    200;}
	
.ui-rotatable-handle {
	background:    #f5dc58;
	border:        1px solid #FFF;
	border-radius: 5px;
		-moz-border-radius:    5px;
		-o-border-radius:      5px;
		-webkit-border-radius: 5px;
	cursor:        pointer;
	
	height:        10px;
	left:          50%;
	margin:        0 0 0 -5px;
	position:      absolute;
	top:           -5px;
	width:         10px;}

.ui-rotatable-handle.clone {
	visibility:  hidden;
}


')
        
        ->registerScript(__FILE__, '

showPartion($("#SceneWidget_type").val());
$("#SceneWidget_type").change(function(){
    showPartion($(this).val());
});

var jsDeviceImages = '.json_encode($jsDeviceImages).';
var jsWidgets = '.json_encode($jsWidgets).';


// ------------------ text ---------------------
$("#wp_type_'.SceneWidget::TYPE_TEXT.' input").change(function(){
    //updateWidgetIcon($("#widgetIcon"));
    $("#widgetIcon").DomoWidget("update");
});
// ------------------ /text ---------------------



// ------------------ devicevalue ---------------------
$("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' select[name=wp_image]").ddslick({
    data: jsDeviceImages,
    width: "100%",
    imagePosition: "left",
    onSelected: function(selectedData){
        var imgValue = selectedData.selectedData.value;
        $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_imagevalue]").val(imgValue);
        //updateWidgetIcon($("#widgetIcon"));
        $("#widgetIcon").DomoWidget("update");
    }
});    
$("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' select[name=wp_deviceID]").change(function(){
    $.ajax({
        url: Yii.app.createUrl("/site/devicevalue", {deviceID: $(this).val()}),
        dataType: "json",
        success: function(data){
            if(data.state == "success"){
                var value = data.value;
                $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_value]").val(value);
                //updateWidgetIcon($("#widgetIcon"));
                $("#widgetIcon").DomoWidget("update");
            } else{
                alertify.error(data.message);
            }
        },
        error: function(){
            alert("error");
        }
    });
});
$("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input").change(function(){
    //updateWidgetIcon($("#widgetIcon"));
    $("#widgetIcon").DomoWidget("update");
});
// ------------------ /devicevalue ---------------------



// ------------------ checkbox ---------------------
$("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' select[name=wp_image0]").ddslick({
    data: jsDeviceImages,
    width: "100%",
    imagePosition: "left",
    onSelected: function(selectedData){
        var imgValue = selectedData.selectedData.value;
        $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input[name=wp_image0value]").val(imgValue);
        //updateWidgetIcon($("#widgetIcon"));
        $("#widgetIcon").DomoWidget("update");
    }
});    
$("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' select[name=wp_image1]").ddslick({
    data: jsDeviceImages,
    width: "100%",
    imagePosition: "left",
    onSelected: function(selectedData){
        var imgValue = selectedData.selectedData.value;
        $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input[name=wp_image1value]").val(imgValue);
        //updateWidgetIcon($("#widgetIcon"));
        $("#widgetIcon").DomoWidget("update");
    }
});
$("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' select[name=wp_deviceID]").change(function(){
    $.ajax({
        url: Yii.app.createUrl("/site/devicevalue", {deviceID: $(this).val()}),
        dataType: "json",
        success: function(data){
            if(data.state == "success"){
                var value = data.value;
                $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input[name=wp_value]").val(value);
                //updateWidgetIcon($("#widgetIcon"));
                $("#widgetIcon").DomoWidget("update");
            } else{
                alertify.error(data.message);
            }
        },
        error: function(){
            alert("error");
        }
    });
});
$("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input").change(function(){
    //updateWidgetIcon($("#widgetIcon"));
    $("#widgetIcon").DomoWidget("update");
});
$("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' select").change(function(){
    //updateWidgetIcon($("#widgetIcon"));
    $("#widgetIcon").DomoWidget("update");
});


// ------------------ /checkbox ---------------------


// ------------------ video ---------------------
$("#wp_type_'.SceneWidget::TYPE_VIDEO.' select[name=wp_image]").ddslick({
    data: jsDeviceImages,
    width: "100%",
    imagePosition: "left",
    onSelected: function(selectedData){
        var imgValue = selectedData.selectedData.value;
        $("#wp_type_'.SceneWidget::TYPE_VIDEO.' input[name=wp_imagevalue]").val(imgValue);
        //updateWidgetIcon($("#widgetIcon"));
        $("#widgetIcon").DomoWidget("update");
    }
});    
$("#wp_type_'.SceneWidget::TYPE_VIDEO.' input").change(function(){
    //updateWidgetIcon($("#widgetIcon"));
    $("#widgetIcon").DomoWidget("update");
});
$("#wp_type_'.SceneWidget::TYPE_VIDEO.' select").change(function(){
    //updateWidgetIcon($("#widgetIcon"));
    $("#widgetIcon").DomoWidget("update");
});

// ------------------ /video ---------------------



// ------------------ devicemultivalue ---------------------
$("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' select[name=wp_image]").ddslick({
    data: jsDeviceImages,
    width: "100%",
    imagePosition: "left",
    onSelected: function(selectedData){
        var imgValue = selectedData.selectedData.value;
        $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' input[name=wp_imagevalue]").val(imgValue);
        //updateWidgetIcon($("#widgetIcon"));
        $("#widgetIcon").DomoWidget("update");
    }
});    
$("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' select[name=wp_deviceID]").change(function(){
    $.ajax({
        url: Yii.app.createUrl("/site/devicevalue", {deviceID: $(this).val()}),
        dataType: "json",
        success: function(data){
            if(data.state == "success"){
                var value = data.value;
                $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' input[name=wp_value]").val(value);
                //updateWidgetIcon($("#widgetIcon"));
                $("#widgetIcon").DomoWidget("update");
            } else{
                alertify.error(data.message);
            }
        },
        error: function(){
            alert("error");
        }
    });
});
$("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' input").change(function(){
    //updateWidgetIcon($("#widgetIcon"));
    $("#widgetIcon").DomoWidget("update");
});
// ------------------ /devicemultivalue ---------------------





//$("#wp_widget_css textarea").change(function(){
//    $("#widgetIcon").DomoWidget("update");
//});
$("#wp_widget_css textarea").on("change keyup paste", function() {
    //alertify.success("change");
    $("#widgetIcon").DomoWidget("update");
});




//alert(123);
$("#widgetIcon").DomoWidget();
//return;


$("#widgetAdd").click(function(){

    $("#widgetID").val("");
    $("#SceneWidget_caption").val("");
    $("#SceneWidget_type").val('.SceneWidget::TYPE_TEXT.');

    $("#wp_type_'.SceneWidget::TYPE_TEXT.' input").val("");

    showPartion($("#SceneWidget_type").val());
    $("#widgetIcon").DomoWidget("update");


});


deviceDragUpdate();
function deviceDragUpdate(){
    $("#deviceList .item").draggable({
        helper: "clone",
        cursor: "move",
        start: function(event, ui){

        }
    });

//    $("#scene_container #scene_devices .item").draggable({
//        helper: "clone",
//        cursor: "move",
//        start: function(event, ui){
//
//        }
//    });
    
//    $("#scene_container #scene_devices .item").each(function(){
//    
//        //alert( $(this).height() );
//        var obj = $("<div></div>");
//        obj.css({
//            border: "1px red solid",
//            backgroundColor: "#888",
//            left: ($(this).width()/2) +"px",
//            top: ($(this).height() + 5) +"px",
//            width: "10px",
//            height: "10px",
//            position: "absolute"
//        });
//        
//        $(this).append(obj);
//    
//    });
    


    var drWr = $("#scene_container #scene_devices .draggable-wrapper");
    var rsWr = $("#scene_container #scene_devices .resizable-wrapper");
    var elem = $("#scene_container #scene_devices .item");

    elem.resizable({
        aspectRatio: false,
        //handles: "ne, nw, se, sw",
        handles: "se",
        stop: function(event, ui){
            //alert(objInfo(event));
            //alert(objInfo(ui.size));
            var id = ui.element.data("device-id");
            //alert(id);
            $.ajax({
                url: "'.Yii::app()->createUrl('site/sceneputdevice', array('sceneID'=>$scModel->id)).'",
                type: "post",
                data: {id: id, width: ui.size.width, height: ui.size.height},
                success: function(data){
//                    var obj = $(data);
//                    $("#deviceList .item[data-device-id="+obj.data("device-id")+"]").remove();
//                    $("#scene_container #scene_devices .item[data-device-id="+obj.data("device-id")+"]").remove();
//                    $("#scene_container #scene_devices").append(obj);
//                    deviceDragUpdate();
//                    deviceDeleteUpdate();
                    
                    alertify.success("Размер изменён");
                },
                error: function(){
                    alert("error");
                },
                complete: function(){
                }
            });
        }
    });

    elem.rotatable({
        //angle: objInfo(this), //$(this).find(".item").data("rotate-angle")*Math.PI/180,
        stop: function(event, ui){
            //alert(ui.element.data("device-id"));
            //alert(objInfo(ui.angle));
            
            var id = ui.element.data("device-id");
            var angle = ui.angle.current;
            angle = Math.floor((angle*180/Math.PI)%360);
            
            //alert( (angle*180/Math.PI)%360 );
            $.ajax({
                url: "'.Yii::app()->createUrl('site/sceneputdevice', array('sceneID'=>$scModel->id)).'",
                type: "post",
                data: {id: id, angle: angle},
                success: function(data){
//                    var obj = $(data);
//                    $("#deviceList .item[data-device-id="+obj.data("device-id")+"]").remove();
//                    $("#scene_container #scene_devices .item[data-device-id="+obj.data("device-id")+"]").remove();
//                    $("#scene_container #scene_devices").append(obj);
//                    deviceDragUpdate();
//                    deviceDeleteUpdate();
                
                    alertify.success("Угол поворота изменён");
                },
                error: function(){
                    alert("error");
                },
                complete: function(){
                }
            });

        }
    });
    
//    elem.each(function(){
//        //alert( objInfo(this) );
//        var angle = $(this).data("rotate-angle");
//        //$(this).angle( angle*Math.PI/180 );
//        //$(this).rotatable({angle: angle*Math.PI/180});
//    });

    drWr.draggable({
        stop: function(event, ui){
            var obj = ui.helper.find(".item");
            var id = obj.data("device-id");

            var p1 = ui.position;
            var p2 = obj.position();
            var x = p2.left + p1.left;
            var y = p2.top + p1.top;

            $.ajax({
                url: "'.Yii::app()->createUrl('site/sceneputdevice', array('sceneID'=>$scModel->id)).'",
                type: "post",
                data: {id: id, x: x, y: y},
                success: function(data){
//                    var obj = $(data);
//                    $("#deviceList .item[data-device-id="+obj.data("device-id")+"]").remove();
//                    $("#scene_container #scene_devices .item[data-device-id="+obj.data("device-id")+"]").remove();
//                    $("#scene_container #scene_devices").append(obj);
//                    deviceDragUpdate();
//                    deviceDeleteUpdate();
                
                    alertify.success("Расположение изменено");
                },
                error: function(){
                    alert("error");
                },
                complete: function(){
                }
            });

        }
    });


    $("#widgetIcon").draggable({
        helper: "clone",
        cursor: "move",
        helper: function(){
            return $(this).clone().appendTo("body");
        },
        start: function(event, ui){

        },
        stop: function(event, ui){

        }
    });

    //$("#widgetIcon").click(function(){
    //    $("#widgetParams").show();
    //});



}


deviceDeleteUpdate();
function deviceDeleteUpdate(){
    $("#scene_container #scene_devices .item .delete").unbind("click");
    $("#scene_container #scene_devices .item .delete").click(function(){
        var id = $(this).parent().data("device-id");
        $.ajax({
            url: "'.Yii::app()->createUrl('site/scenedeletedevice', array('sceneID'=>$scModel->id)).'",
            type: "post",
            data: {id: id},
            success: function(data){
                if(data != ""){
                    $("#scene_container #scene_devices .item[data-device-id="+data+"]").remove();
                    $("#deviceList").load(window.location.href+" #deviceList", function(response, status, xhr){
                        deviceDragUpdate();
                    });
                }
            },
            error: function(){
                alert("error");
            },
            complete: function(){
            }
        });
    });
}



$("#scene_container").droppable({
    drop: function(event, ui){
        //var id = $(event.srcElement).data("id");
        var id = ui.helper.data("device-id");
        var containerName = ui.helper.parent().attr("id");
        if(!containerName) containerName = ui.helper.attr("id");

        var posHelper = ui.helper.position();
        var posContainer = $("#scene_container").position();
        
        var left = posHelper.left;
        var top = posHelper.top;
        
        var ofsLeft = $("#scene_container").scrollLeft();
        var ofsTop = $("#scene_container").scrollTop();
        
        var x, y;
        if(containerName == "deviceList"){
            x = posHelper.left-posContainer.left+ofsLeft;
            y = posHelper.top-posContainer.top+ofsTop;
        } else if(containerName == "scene_devices"){
            x = posHelper.left;
            y = posHelper.top;
        }

        if(containerName == "widgetIcon"){
            //alert("widget icon");
            x = posHelper.left-posContainer.left+ofsLeft;
            y = posHelper.top-posContainer.top+ofsTop;
            //alert("x: "+x+"; y: "+y);
            
            var data = {x: x, y: y};
            //data = $.extend(data, $("#wp_type_"+$("#SceneWidget_type").val()+" form").serializeArray());
            
            var arr = $("form#widget-params-form").serializeArray();
            //alert(objInfo(arr));
            //alert( $("#widgetParams #commonParams").html() );
            for(var ind in arr){
                data[arr[ind].name] = arr[ind].value;
            }
            //alert( objInfo(data) );
            arr = $("#wp_type_"+$("#SceneWidget_type").val()+" form").serializeArray();
            for(var ind in arr){
                data[arr[ind].name] = arr[ind].value;
            }

            arr = $("#wp_widget_css form").serializeArray();
            for(var ind in arr){
                data[arr[ind].name] = arr[ind].value;
            }
            //alert( objInfo(data) );
            //return;

            $.ajax({
                url: "'.Yii::app()->createUrl('site/sceneputwidget', array('sceneID'=>$scModel->id)).'",
                type: "post",
                data: data,
                dataType: "json",
                success: function(data){
                    //alert(data);
                    if(data.state == "success"){
                        //alert(objInfo(data.attributes));
                        var attrs = data.attributes;
                        jsWidgets[attrs.id] = attrs;
                        updateWidgets();
                        
                    } else{
                        alertify.error(data.message);
                    }
                },
                error: function(){
                    alert("error");
                },
                complete: function(){
                }
            });
            
        } else{
            alert("containerName: "+containerName);
        }
    },
    over: function(event, ui){
        
    },
    out: function(event, ui){
        
    }

});



function showPartion(ind){
    $("#widgetParams .form .partion").hide();
    $("#widgetParams .form #wp_type_"+ind).show();
    
}

//function updateWidgetIcon(iconObj, params){
//
//    var type = null;
//    var defParams = true;
//    if(!params){
//        defParams = false;
//        params = {};
//        params.widgetType = $("#SceneWidget_type").val();
//    }
//
//    if(params.widgetType == '.SceneWidget::TYPE_TEXT.'){
//        if(!defParams){
//            params.wp_text = $("#wp_type_'.SceneWidget::TYPE_TEXT.' input[name=wp_text]").val();
//            params.wp_fontsize = $("#wp_type_'.SceneWidget::TYPE_TEXT.' input[name=wp_fontsize]").val();
//            params.wp_color = $("#wp_type_'.SceneWidget::TYPE_TEXT.' input[name=wp_color]").val();
//        }
//        iconObj.text( params.wp_text );
//        if(params.wp_fontsize != "") iconObj.css("font-size", params.wp_fontsize);
//        if(params.wp_color != "") iconObj.css("color", params.wp_color);
//        
//    } else if(params.widgetType == '.SceneWidget::TYPE_DEVICEVALUE.'){
//        if(!defParams){
//            params.wp_template = $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_template]").val();
//
//            params.wp_imagevalue = $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_imagevalue]").val();
//
//            params.wp_value = $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_value]").val();
//            params.wp_imagewidth = $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_imagewidth]").val();
//            params.wp_imageheight = $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_imageheight]").val();
//            params.wp_valuefontsize = $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_valuefontsize]").val();
//            params.wp_valuecolor = $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_valuecolor]").val();
//        }
//
//        var imgSrc = "'.Yii::app()->baseUrl.'/images/icons/"+params.wp_imagevalue;
//
//        var style = "";
//        style += params.wp_imagewidth != "" ? "width: "+params.wp_imagewidth+"px; " : "";
//        style += params.wp_imageheight != "" ? "height: "+params.wp_imageheight+"px; " : "";
//
//        var imgContent = "<img src=\""+imgSrc+"\""+(style != "" ? " style=\""+style+"\"" : "")+"/>";
//        
//        style = "";
//        style += params.wp_valuefontsize != "" ? "font-size: "+params.wp_valuefontsize+"px; " : "";
//        style += params.wp_valuecolor != "" ? "color: "+params.wp_valuecolor+"; " : "";
//
//        var valueContent = "<span"+(style != "" ? " style=\""+style+"\"" : "")+">"+params.wp_value+"</span>";
//
//        var val = params.wp_template.replace(/\{\$image\}/g, imgContent);
//        val = val.replace(/\{\$value\}/g, valueContent);
//        iconObj.html( val );
//    } else if(params.widgetType == '.SceneWidget::TYPE_CHECKBOX.'){
//        if(!defParams){
//            params.wp_image0value = $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input[name=wp_image0value]").val();
//            params.wp_image1value = $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input[name=wp_image1value]").val();
//            params.wp_imagewidth = $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input[name=wp_imagewidth]").val();
//            params.wp_imageheight = $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input[name=wp_imageheight]").val();
//        }
//        
//        var imgSrc = "'.Yii::app()->baseUrl.'/images/icons/"+params.wp_image0value;
//        
//        var style = "";
//        style += params.wp_imagewidth != "" ? "width: "+params.wp_imagewidth+"px; " : "";
//        style += params.wp_imageheight != "" ? "height: "+params.wp_imageheight+"px; " : "";
//
//        var imgContent = "<img src=\""+imgSrc+"\""+(style != "" ? " style=\""+style+"\"" : "")+"/>";
//
//        var val = imgContent;
//        iconObj.html( val );
//    } else if(params.widgetType == '.SceneWidget::TYPE_VIDEO.'){
//        if(!defParams){
//            params.wp_url = $("#wp_type_'.SceneWidget::TYPE_VIDEO.' input[name=wp_url]").val();
//            params.wp_imagewidth = $("#wp_type_'.SceneWidget::TYPE_VIDEO.' input[name=wp_imagewidth]").val();
//            params.wp_imageheight = $("#wp_type_'.SceneWidget::TYPE_VIDEO.' input[name=wp_imageheight]").val();
//            params.wp_imagevalue = $("#wp_type_'.SceneWidget::TYPE_VIDEO.' input[name=wp_imagevalue]").val();
//            params.wp_stream = $("#wp_type_'.SceneWidget::TYPE_VIDEO.' select[name=wp_stream]").val();
//        }
//
//        var style = "";
//        style += params.wp_imagewidth != "" ? "width: "+params.wp_imagewidth+"px; " : "";
//        style += params.wp_imageheight != "" ? "height: "+params.wp_imageheight+"px; " : "";
//
//        //var imgSrc = "'.Yii::app()->baseUrl.'/images/icons/"+params.wp_imagevalue;
//            
//        var imgSrc = "";
//        if(params.wp_stream == 1){
//            imgSrc = params.wp_url;
//        } else{
//            imgSrc = "'.Yii::app()->baseUrl.'/images/icons/"+params.wp_imagevalue;
//        }
//
//        //var imgContent = "<img src=\""+params.wp_url+"\""+(style != "" ? " style=\""+style+"\"" : "")+"/>";
//        var imgContent = "<img src=\""+imgSrc+"\""+(style != "" ? " style=\""+style+"\"" : "")+"/>";
//
//        var val = imgContent;
//        iconObj.html( val );
//    }
//    
//    if(!defParams && $("#widgetID").val() != ""){
//        updateWidgetIcon($("#scene_container #scene_widgets .item[data-widget-id="+$("#widgetID").val()+"]"), params);
//    }
//
//}




updateWidgets();

function updateWidgets(){

    $("#scene_container #scene_widgets").html("");

    for(var widgetID in jsWidgets){
        var widgetData = jsWidgets[widgetID];
        var params = widgetData.params;

        params["widgetType"] = widgetData.type;
        //params["widgetID"] = widgetData.id;

        var obj = $("<div class=\"item\" data-widget-id=\""+widgetData.id+"\"></div>");
        //var obj = $("<div></div>");

    //    obj
    //        .css("border", "1px #DDD solid")
    //        .css("background-color", "#EEE")
    //        .css("padding", "5px")
    //        ;

        obj
            .css("left", params.x+"px")
            .css("top", params.y+"px")
            ;


        obj.DomoWidget(params);


        //updateWidgetIcon(obj, params);

        $("#scene_container #scene_widgets").append(obj);

        obj.DomoWidget("click", function(){

            var widgetID = $(this).data("widget-id");
            var widgetData = jsWidgets[widgetID];
            var params = widgetData.params;

            $("#SceneWidget_type").val(widgetData.type);
            $("#SceneWidget_caption").val(widgetData.caption);
            $("#widgetID").val(widgetData.id);

            showPartion(widgetData.type);

            if(widgetData.type == '.SceneWidget::TYPE_TEXT.'){

                $("#wp_type_'.SceneWidget::TYPE_TEXT.' input[name=wp_text]").val(params.wp_text)
                $("#wp_type_'.SceneWidget::TYPE_TEXT.' input[name=wp_fontsize]").val(params.wp_fontsize)
                $("#wp_type_'.SceneWidget::TYPE_TEXT.' input[name=wp_color]").val(params.wp_color)

            } else if(widgetData.type == '.SceneWidget::TYPE_DEVICEVALUE.'){

                $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' select[name=wp_deviceID]").val(params.wp_deviceID);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' select[name=wp_deviceID]").change();
                $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_template]").val(params.wp_template);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_imagewidth]").val(params.wp_imagewidth);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_imageheight]").val(params.wp_imageheight);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_valuefontsize]").val(params.wp_valuefontsize);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' input[name=wp_valuecolor]").val(params.wp_valuecolor);

                for(var ind in jsDeviceImages){
                    if(jsDeviceImages[ind].value == params.wp_imagevalue) break;
                }
                $("#wp_type_'.SceneWidget::TYPE_DEVICEVALUE.' #wp_image").ddslick("select", {index: ind});

            } else if(widgetData.type == '.SceneWidget::TYPE_CHECKBOX.'){

                $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' select[name=wp_deviceID]").val(params.wp_deviceID);
                $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' select[name=wp_deviceID]").change();

                $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input[name=wp_imagewidth]").val(params.wp_imagewidth);
                $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' input[name=wp_imageheight]").val(params.wp_imageheight);
                $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' select[name=wp_interact]").val(params.wp_interact);

                for(var ind in jsDeviceImages){
                    if(jsDeviceImages[ind].value == params.wp_image0value) break;
                }
                $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' #wp_image0").ddslick("select", {index: ind});
                for(var ind in jsDeviceImages){
                    if(jsDeviceImages[ind].value == params.wp_image1value) break;
                }
                $("#wp_type_'.SceneWidget::TYPE_CHECKBOX.' #wp_image1").ddslick("select", {index: ind});

            } else if(widgetData.type == '.SceneWidget::TYPE_VIDEO.'){

                $("#wp_type_'.SceneWidget::TYPE_VIDEO.' input[name=wp_url]").val(params.wp_url);
                $("#wp_type_'.SceneWidget::TYPE_VIDEO.' input[name=wp_imagewidth]").val(params.wp_imagewidth);
                $("#wp_type_'.SceneWidget::TYPE_VIDEO.' input[name=wp_imageheight]").val(params.wp_imageheight);
                $("#wp_type_'.SceneWidget::TYPE_VIDEO.' select[name=wp_stream]").val(params.wp_stream);
                for(var ind in jsDeviceImages){
                    if(jsDeviceImages[ind].value == params.wp_imagevalue) break;
                }
                $("#wp_type_'.SceneWidget::TYPE_VIDEO.' #wp_image").ddslick("select", {index: ind});

            } else if(widgetData.type == '.SceneWidget::TYPE_DEVICEMULTIVALUE.'){

                $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' select[name=wp_deviceID]").val(params.wp_deviceID);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' select[name=wp_deviceID]").change();
                $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' input[name=wp_template]").val(params.wp_template);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' input[name=wp_imagewidth]").val(params.wp_imagewidth);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' input[name=wp_imageheight]").val(params.wp_imageheight);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' input[name=wp_valuefontsize]").val(params.wp_valuefontsize);
                $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' input[name=wp_valuecolor]").val(params.wp_valuecolor);

                for(var ind in jsDeviceImages){
                    if(jsDeviceImages[ind].value == params.wp_imagevalue) break;
                }
                $("#wp_type_'.SceneWidget::TYPE_DEVICEMULTIVALUE.' #wp_image").ddslick("select", {index: ind});

            }

            //alert(params.wp_css);
            $("#wp_widget_css textarea[name=wp_css]").val(params.wp_css);


            //updateWidgetIcon($("#widgetIcon"));
            $("#widgetIcon").DomoWidget("update");


        });


    }
}



'); ?>
