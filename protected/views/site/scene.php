<?

$this->pageTitle = "Сцена" ." : ". Yii::app()->name;

//$this->layout = 'simple';

$scModel = null;
$data = array();
foreach($sceneModels as $n=>$sceneModel){
    if(($id === null && !$n) || ($sceneModel->id == $id)){
        $scModel = $sceneModel;
    }
    $data[$sceneModel->id] = $sceneModel->caption;
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

<div id="connectStatus" data-status="0"></div>

<div id="pluginToggle" style="width: 20px; height: 20px; background-color: #EEE; padding: 5px 5px 0 5px; position: relative; cursor: pointer;"> <div style="background-color: #CCC; margin-bottom: 3px; height: 3px;"></div><div style="background-color: #CCC; margin-bottom: 3px; height: 3px;"></div><div style="background-color: #CCC; margin-bottom: 3px; height: 3px;"></div> </div>

<div style="position: relative;">
    <div id="pluginContainer">
        <? foreach($pluginModels as $pluginModel): ?>
        <div class="item" data-plugin-id="<?=$pluginModel->id?>" data-plugin-name="<?=$pluginModel->name?>">
            <div class="caption"><?=CHtml::encode($pluginModel->caption)?></div>
            <div class="content">
                <? $plugin = new $pluginModel->name($pluginModel); ?>
                <? $plugin->render(); ?>
            </div>
        </div>
        <? endforeach; ?>
    
        <div>
            <ul>
                <li id="rebootsystem"><?=CHtml::link('Перезапуск системы', array('/site/rebootsystem'), array('onclick'=>'alertify.confirm("Перезагрузить систему?", function(e){if(e){ $("#rebootsystem a").load($("#rebootsystem a").attr("href"), function(){ alertify.error("!!! Перезагрузка системы... !!!"); }) }}); return false;', 'style'=>'color: #F00;'))?></li>
            </ul>
        </div>
        
        <? if(sizeof($logMainModels) > 0): ?>
            <ul id="logMain">
            <? foreach($logMainModels as $logMainModel): ?>
                <li><?=date("H:i", $logMainModel->logtime)?>: <?=trim(preg_replace("%(\n)in /.+\)%Ui", "$1", $logMainModel->message))?></li>
            <? endforeach; ?>
            </ul>
        <? endif; ?>

    </div>
</div>

<div style="float: right;"><?=CHtml::link('Редактировать', array('site/sceneupdate', 'id'=>$scModel->id))?></div>

<?=CHtml::dropDownList('scene', $id, $data)?>

<div id="scene_container" style="overflow: none; height: 900px;">
    <div id="scene_image">
        <?=CHtml::image(Yii::app()->baseUrl.'/images/scenes/'.$scModel->image, '', array())?>
    </div>
    
    <div id="scene_widgets"></div>
</div>

<br/>
<div class="clear"></div>

<div id="log"></div>

<div id="test"></div>
    
<? Yii::app()->clientScript
        
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jQueryRotateCompressed.js')

        ->registerScriptFile(Yii::app()->baseUrl.'/js/stomp/sockjs-0.3.min.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/stomp/stomp.js')

        ->registerScriptFile(Yii::app()->baseUrl.'/js/DomoWidget.js')
        
        ->registerScript('scene-script-code', '

$.fn.exists = function(){return ($(this).length > 0);}
$.exists = function(selector) {return ($(selector).length > 0);}

var jsDeviceImages = '.json_encode($jsDeviceImages).';
var jsWidgets = '.json_encode($jsWidgets).';
    
$("select[name=scene]").change(function(){
    var id = $(this).val();
    window.location.href="'.Yii::app()->createUrl('site/scene').'&id="+id;
});


WebSocketStompMock = SockJS;
var client = Stomp.client("https://"+window.location.hostname+":'.Yii::app()->params['amqp']['port'].'/stomp");

client.debug = function(m, p){
    $("#log").prepend("<p class=\"rabbitmq\">"+m+"</p>");
}

updateRabbitMQ();
function updateRabbitMQ(){
    client.connect("'.Yii::app()->amqp->login.'", "'.Yii::app()->amqp->password.'", function(x) {
        $("#log").prepend("<p>RabbitMQ connected</p>");
        var id = client.subscribe("/topic/domoWebPage", function(message) {
            var destination = message.destination;
            var data = $.trim(message.body);

            if($("#log").height() > 150) $("#log").height(150);

            if(data != "" && data.substr(0, 1) == "{" && data.substr(data.length-1, 1) == "}"){
                eval("var d="+data+";");
                parseRecieve(d);
            }
        });
        
        $("#rebootsystem a").text("Перезапуск системы");
        
        alertify.success("Система работает.");
        
        $("#connectStatus")
            .attr("data-status", 1)
            .attr("title", "Система работает.")
        ;

    }, function(){
        // переподключение в случае обрыва соединения
        setTimeout(updateRabbitMQ, 10*1000);
        $("connectStatus")
            .attr("data-status", 0)
            .attr("title", "Система не работает.")
        ;
    });
}


var parseRecieve = function(d){
    for(var ind in d.recieve){
        var rec = d.recieve[ind];

        if(rec["command"] == "1WIRE_LIST"){
        
        } else if(rec["command"] == "DEVICE_VALUE"){
            DomoWidget.AMQPRecieve(rec);
        } else if(rec["command"] == "NFC_DATA"){
            DomoWidget.AMQPRecieve(rec);
        } else if(rec["command"] == "PLUGIN_REFRESH"){
            var pluginID = rec["ID"];
            $("#pluginContainer .item[data-plugin-id="+pluginID+"] .content").load(Yii.app.createUrl("/plugin/renderItem", {id: pluginID}));
            
            if(rec["state"] == "success"){
                alertify.success(rec["message"]);
            } else if(rec["state"] == "error"){
                alertify.error(rec["message"]);
            } else{
                alertify.notify(rec["message"]);
            }
        }
    }
}


$("#pluginToggle").click(function(){
    if($("#pluginContainer").css("display") == "none"){
        $("#pluginContainer").show();
    } else{
        $("#pluginContainer").hide();
    }
});


for(var widgetID in jsWidgets){
    var widgetData = jsWidgets[widgetID];
    var params = widgetData.params;
    
    params["widgetType"] = widgetData.type;
    params["widgetID"] = widgetData.id;

    var obj = $("<div class=\"item\" data-widget-id=\""+widgetData.id+"\"></div>");
    
    obj
        .css("border", "1px #DDD solid")
        .css("background-color", "#EEE")
        .css("padding", "5px")
        ;
        
    obj
        .css("left", params.x+"px")
        .css("top", params.y+"px")
        ;


    obj.DomoWidget(params);

    $("#scene_container #scene_widgets").append(obj);

}



DomoWidget.AMQPSend(function(params){
    if(client){
        var command = JSON.stringify(params);
        client.send("/topic/domoWebProcess", {}, command);
        $("#log").prepend("<p>&gt;&gt;&gt; "+command+"</p>");
    }
});


setInterval(function(){
    if(client){
        client.send("/topic/domoping", {}, "ping");
    }
}, 60*1000);


setInterval(function(){
    var maxlen = 10000;
    var str = $("#log").html();
    if(str.length > maxlen){
        $("#log").html(str.substr(0, maxlen)+" ...");
    }
});


'); ?>

<?

$this->widget('application.extensions.fancybox.EFancyBox', array(
//    'target' => '#scene_devices .item .graph a',
//    'config' => array(
//        'type' => 'iframe',
//        'autoSize' => true,
//        'width' => '95%',
//        //'href' => 'js:Yii.app.createUrl("/site/historygraph", {deviceID:$(this).attr("data-id")})',
//        //'beforeLoad' => 'js:function(){ this.href = Yii.app.createUrl("/site/historygraph", {deviceID:this.element.data("device-id")});  }',
//    ),
));

?>




