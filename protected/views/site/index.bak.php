<?php

$this->pageTitle = "Исполнительные устройства" ." : ". Yii::app()->name;

?>

<? //phpinfo(32); ?>

<!-- <img src="http://<?=$_SERVER['SERVER_NAME']?>:8080?action=stream"/> -->
<!-- <img src="<?=Yii::app()->createUrl('site/test')?>"/> -->
<?//=CHtml::image(Yii::app()->createUrl('site/test'))?>

<!--
<div id="dev_1">
    <div class="caption">Светодиод 1</div>
    <div class="value">&nbsp;</div>
    <div class="action"><?=CHtml::link('Включить', '', array())?></div>
</div>
-->

<!-- <iframe src="<?=Yii::app()->createUrl('site/testvideo')?>"></iframe> -->


<? foreach($executiveDevices as $executiveDevice): ?>
<?
    $controllerModel = null;
    if($executiveDevice->parent->type == DEV_ARDUINO) $controllerModel = $executiveDevice->parent;
    else $controllerModel = $executiveDevice->parent->parent;
    $model = $executiveDevice;
?>
<div id="dev_<?=$model->id?>" data-controller-id="<?=$controllerModel->id?>" data-device-id="<?=$executiveDevice->id?>" class="device type_<?=array_search($model->type, Yii::app()->params['defineDevices'])?>">
    <div class="caption"><?=CHtml::encode($model->caption)?></div>
    <? if($model->type == DEV_LED): ?>
        <div class="value"><?=CHtml::image($model->value?'images/icons/light_on.png':'images/icons/light_off.png')?></div>
        <div class="action <?=$model->value?'on':'off'?>"><?=$model->value?'выключить':'включить'?></div>
    <? elseif($model->type == DEV_BUTTON): ?>
        <div class="value"><?=CHtml::image($model->value?'images/icons/button_on.png':'images/icons/button_off.png')?></div>
    <? elseif($model->type == DEV_MOVESENSOR): ?>
        <div class="value"><?=CHtml::image($model->value?'images/icons/move_on.png':'images/icons/move_off.png')?></div>
    <? elseif($model->type == DEV_TEMPERATURESENSOR): ?>
        <div class="image"><?=CHtml::image('images/icons/termo.png')?></div>
        <div class="value"><span><?=$model->value?>&nbsp;</span><sup>&deg;C</sup></div>
        <div class="action">обновить</div>
    <? elseif($model->type == DEV_RELE): ?>
        <div class="value"><?=CHtml::image($model->value?'images/icons/light_on.png':'images/icons/light_off.png')?></div>
        <div class="action <?=$model->value?'on':'off'?>"><?=$model->value?'выключить':'включить'?></div>
    <? elseif($model->type == DEV_MAGNETOSENSOR): ?>
        <div class="value"><?=CHtml::image($model->value?'images/icons/magneto_on.png':'images/icons/magneto_off.png')?></div>
    <? elseif($model->type == DEV_VIBROSENSOR): ?>
        <div class="value"><?=CHtml::image($model->value?'images/icons/vibro_on.png':'images/icons/vibro_off.png')?></div>
    <? endif; ?>
</div>
<hr/>
<? endforeach;?>



<!-- <div id="log" style="width:500px; height: 400px; border: 1px solid #999999; overflow:auto;"></div> -->
<!-- <div id="log" style="border: 1px solid #999999;"></div> -->
<div id="log" style="border-right: 1px solid #999; border-bottom: 1px solid #999; border-top: 1px solid #BBB; border-left: 1px solid #BBB; background-color: #EEE; font-family: 'Courier New'; color: #000; padding: 5px; overflow: auto;"></div>



<?
$jsDevices = '';
foreach(Yii::app()->params['defineDevices'] as $def=>$val){
    $jsDevices .= "var {$def} = {$val};" ."\n";
}

?>

<? Yii::app()->getClientScript()->registerCoreScript('jquery')
        ->registerScript('script-code', '



var ws;
startWS();
function startWS(){
    if("WebSocket" in window){
        ws = new WebSocket("wss://"+document.domain+":8047/domoApp?cs='./*$cpe*/''.'");
        //ws = new WebSocket("wss://"+document.domain+":8047/domoApp");
        ws.onopen = function(){
            $("#log").prepend("<p>WebSocket opened</p>");
        }
        ws.onmessage = function (e) {
            var data = $.trim(e.data);
            //document.getElementById("log").innerHTML += "WebSocket message: "+e.data+" <br/>";
            //$("#log").append("<p>"+e.data+"</p>");
            //alert(e.data);
            if(data != "pong"){
                $("#log").prepend("<p>&lt;&lt;&lt; "+e.data+"</p>");
            }
            if($("#log").height() > 150) $("#log").height(150);

            if(data != "" && data.substr(0, 1) == "{" && data.substr(data.length-1, 1) == "}"){
                eval("var d="+data+";");

                for(var ind in d.recieve){
                    var rec = d.recieve[ind];
                    //alert(rec["command"]);
                    if(rec["command"] == "DEVICE_VALUE"){
                        var value = rec["value"];
                        var devObj = $("#dev_"+rec["ID"]);
                        //alert(value);
                        if(value == null || value == "")
                            value = "null";
                        //$("#executiveDevices li[data-device-id="+rec["ID"]+"] span span").html(value);
                        if(devObj.hasClass("type_DEV_TEMPERATURESENSOR")){
                            devObj.find(".value span").html(value);
                        }
                    }
                }

/*
                for(var i=0; i<d["recieve"].length; i++){
                    var dev = d["recieve"][i];
                    var value = dev["value"];
                    var devObj = $("#dev_"+dev["ID"]);

                    if(devObj.hasClass("type_DEV_LED")){
                        //devObj.find(".value").html(value);
                        if(value == 1){
                            devObj.find(".value img").attr("src", "images/icons/light_on.png");
                            devObj.find(".action").removeClass("off").addClass("on").html("выключить");
                        } else{
                            devObj.find(".value img").attr("src", "images/icons/light_off.png");
                            devObj.find(".action").removeClass("on").addClass("off").html("включить");
                        }
                    } else if(devObj.hasClass("type_DEV_BUTTON")){
                        //devObj.find(".value").html(value);
                        if(value == 1){
                            devObj.find(".value img").attr("src", "images/icons/button_on.png");
                        } else{
                            devObj.find(".value img").attr("src", "images/icons/button_off.png");
                        }
                    } else if(devObj.hasClass("type_DEV_MOVESENSOR")){
                        if(value == 1){
                            devObj.find(".value img").attr("src", "images/icons/move_on.png");
                        } else{
                            devObj.find(".value img").attr("src", "images/icons/move_off.png");
                        }
                    } else if(devObj.hasClass("type_DEV_TEMPERATURESENSOR")){
                        devObj.find(".value span").html(value);
                    } else if(devObj.hasClass("type_DEV_RELE")){
                        if(value == 1){
                            devObj.find(".value img").attr("src", "images/icons/light_on.png");
                            devObj.find(".action").removeClass("off").addClass("on").html("выключить");
                        } else{
                            devObj.find(".value img").attr("src", "images/icons/light_off.png");
                            devObj.find(".action").removeClass("on").addClass("off").html("включить");
                        }
                    } else if(devObj.hasClass("type_DEV_MAGNETOSENSOR")){
                        if(value == 1){
                            devObj.find(".value img").attr("src", "images/icons/magneto_on.png");
                        } else{
                            devObj.find(".value img").attr("src", "images/icons/magneto_off.png");
                        }
                    } else if(devObj.hasClass("type_DEV_VIBROSENSOR")){
                        if(value == 1){
                            devObj.find(".value img").attr("src", "images/icons/vibro_on.png");
                        } else{
                            devObj.find(".value img").attr("src", "images/icons/vibro_off.png");
                        }
                    }

                }
*/                
            }
        }
        ws.onclose = function () {
            $("#log").prepend("<p>WebSocket closed</p>");
            startWS();
        }

        setInterval(function(){
            if(ws){
                ws.send("ping");
            }
        }, 1*60*1000);


    } else{
        //alert("WebSocket NOT supported");
    }
}


'.$jsDevices.'

var timeUpload = "'.date('YmdHis').'";

$(".device.type_DEV_LED .value img").click( function(){ $(this).parent().parent().find(".action").click(); } );
$(".device.type_DEV_RELE .value img").click( function(){ $(this).parent().parent().find(".action").click(); } );

$(".device.type_DEV_LED .action").click(function(){
    var id = $(this).parent().attr("id").replace("dev_", "");
    var on = $(this).hasClass("on");

    if(ws){
        ws.send("{\"command\":'.CMD_WRITE.', \"deviceID\":"+id+", \"value\":\""+(on?"0":"1")+"\"}");
    }
});


$(".device.type_DEV_TEMPERATURESENSOR .action").click(function(){
//    var id = $(this).parent().attr("id").replace("dev_", "");
    var dev = $(this).parent();

    if(ws){
        var command = "{\"controllerID\":"+dev.data("controller-id")+", \"command\":'.CMD_READ.', \"deviceID\":"+dev.data("device-id")+", \"value\":null}";
        ws.send(command);
        $("#log").prepend("<p>&gt;&gt;&gt; "+command+"</p>");
    }

/*
    $.ajax({
        url: "'.Yii::app()->createUrl('/arduino/command', array('type'=>CMD_READ)).'&deviceID="+id+"&value=null",
        type: "post",
        success: function(data){
            //alert(data);
        },
        error: function(){
            alert("error");
        }, 
        complete: function(){

        }
    });
*/

});

$(".device.type_DEV_RELE .action").click(function(){
    var id = $(this).parent().attr("id").replace("dev_", "");
    var on = $(this).hasClass("on");

    if(ws){
        ws.send("{\"command\":'.CMD_WRITE.', \"deviceID\":"+id+", \"value\":\""+(on?"0":"1")+"\"}");
    }
});




'); ?>
