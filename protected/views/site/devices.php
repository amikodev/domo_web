<?

$this->pageTitle = "Устройства" ." : ". Yii::app()->name;

$onewirePins = array();

$controllerSerials = array();

?>

<h3>Controllers</h3>
<ul id="controllers">
<? foreach($controllerDevices as $controllerDevice): ?>
    <? $jsonParams = json_decode($controllerDevice->params, true); ?>
    <? $controllerSerials[] = $jsonParams['serial']; ?>
    <li data-contoller-id="<?=$controllerDevice->id?>"><?=$controllerDevice->id?>:<?=$controllerDevice->caption?> &rarr; 
        <span class="port <?=in_array($jsonParams['serial'], $usbSerials)?"exists":""?>"><?=$jsonParams['serial']?></span>
    </li>
<? endforeach; ?>
</ul>

<h3>USB Serial</h3>
<? if(sizeof($usbSerials) == 0): ?>
<i>devices not found</i> <br/><br/>
<? else: ?>
    <ul id="usbDevices">
    <? foreach($usbSerials as $n=>$usbSerial): ?>
        <li>
            <span class="<?=in_array($usbSerial, $controllerSerials)?"exists":""?>"><?=$usbSerial?></span>
            <? if(Yii::app()->systemParam->get('excluded-controllers') !== null && in_array($usbSerial, explode(" | ", Yii::app()->systemParam->get('excluded-controllers')->value))): ?>
            <span class="excluded">[excluded]</span>
            <? endif; ?>
        </li>
    <? endforeach; ?>
    </ul>
<? endif; ?>

<h3>Video Devices</h3>
<? if(sizeof($videoDevices) == 0): ?>
<i>devices not found</i> <br/><br/>
<? else: ?>
    <ul id="videoDevices">
    <? foreach($videoDevices as $n=>$videoDevice): ?>
        <li><span><?=$videoDevice?></span></li>
    <? endforeach; ?>
    </ul>
<? endif; ?>

<h3>1Wire Devices</h3>
<? if(sizeof($onewireDevices) == 0): ?>
<i>devices not found</i> <br/><br/>
<? else: ?>
    <ul id="onewireDevices">
    <? foreach($onewireDevices as $n=>$onewireDevice): ?>
        <li data-device-id="<?=$onewireDevice->id?>">
            <?=$onewireDevice->id?>:<?=$onewireDevice->caption?> ("<?=$onewireDevice->parent->caption?>": <?=$onewireDevice->pin?>) 
            [<span class="onewireID" data-controller-id="<?=$onewireDevice->parent->id?>" data-pin="<?=$onewireDevice->pin?>" data-onewire-id="<?=$onewireDevice->onewireID?>"><?=$onewireDevice->onewireID?></span>]
        </li>
        <? $onewirePins[$onewireDevice->parent->id][$onewireDevice->pin] = $onewireDevice->id; ?>
    <? endforeach; ?>
    </ul>
<? endif; ?>

<div id="onewireDevices_other_container" style="display: none;">
<h3>Unknown 1Wire Devices</h3>
    <ul id="onewireDevices_other">
    </ul>
</div>

<h3>I2C Devices</h3>
<? if(sizeof($i2cDevices) == 0): ?>
<i>devices not found</i> <br/><br/>
<? else: ?>
    <ul id="i2cDevices">
    <? foreach($i2cDevices as $n=>$i2cDevice): ?>
        <li><span><?=$i2cDevice->id?>:<?=$i2cDevice->caption?> ("<?=$i2cDevice->parent->caption?>")</span></li>
    <? endforeach; ?>
    </ul>
<? endif; ?>


<h3>Executive Devices</h3>
<? if(sizeof($executiveDevices) == 0): ?>
<i>devices not found</i> <br/><br/>
<? else: ?>
    <ul id="executiveDevices">
    <? foreach($executiveDevices as $n=>$executiveDevice): ?>
        <?
            $controllerModel = null;
            if($executiveDevice->parent->type == DEV_ARDUINO) $controllerModel = $executiveDevice->parent;
            else $controllerModel = $executiveDevice->parent->parent;
        ?>
        <li data-controller-id="<?=$controllerModel->id?>" data-device-id="<?=$executiveDevice->id?>" 
                <?=$executiveDevice->connectType == CONNECT_ONEWIRE ? 'data-onewire-id="'.$executiveDevice->onewireID.'"' : ($executiveDevice->parent->connectType == CONNECT_ONEWIRE ? 'data-onewire-id="'.$executiveDevice->parent->onewireID.'"':'')?>
                <?=$executiveDevice->connectType == CONNECT_ONEWIRE ? 'data-onewire-pin="'.$executiveDevice->pin.'"' : ($executiveDevice->parent->connectType == CONNECT_ONEWIRE ? 'data-onewire-pin="'.$executiveDevice->parent->pin.'"':'')?>
            >
            <span class="inner">
                <?=$executiveDevice->id?>:<span class="caption"><?=$executiveDevice->caption?></span>
                ("<?=$controllerModel->caption?>"
                <? if($executiveDevice->parent->type != DEV_ARDUINO): ?>
                -> "<?=$executiveDevice->parent->caption?>"
                <? endif; ?>
                : 
                <? if($executiveDevice->connectType == CONNECT_I2C): ?>
                    I2C
                <? else: ?>
                    <?=$executiveDevice->pin?>
                <? endif; ?>
                )
                <span class="valueOuter">[<span class="value"><?=$executiveDevice->value === null ? "null" : $executiveDevice->value?></span>]</span>
            </span>
        </li>
    <? endforeach; ?>
    </ul>
<? endif; ?>



<div id="log"></div>

<? Yii::app()->clientScript->registerCoreScript('jquery')
        
        ->registerScriptFile(Yii::app()->baseUrl.'/js/stomp/sockjs-0.3.min.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/stomp/stomp.js')
        
        ->registerCss('devices-style-code', '

#onewireDevices_other li span{
    color: #B00;
    font-family: monospace;
}

#controllers li span.port{
    font-family: monospace;
    color: #BBB;
}
#controllers li span.port.exists{
    color: #BB0;
}

#usbDevices li span{
    font-family: monospace;
}
#usbDevices li span.exists{
    color: #BB0;
}
#usbDevices li span.excluded{
    color: #B00;
    font-family: "Courier New";
}

#onewireDevices li span.onewireID{
    font-family: monospace;
}

#videoDevices li span{
    font-family: monospace;
}


#executiveDevices li span.valueOuter, #executiveDevices li[data-onewire-id].enabled span.valueOuter{
    font-family: monospace;
    cursor: pointer;
}
#executiveDevices li[data-onewire-id].disabled span.inner{
    color: #BBB;
}
#executiveDevices li[data-onewire-id].enabled span.inner{
    color: inherit;
}
#executiveDevices li[data-onewire-id].disabled span.valueOuter{
    cursor: default;
}


')
        ->registerScript('devices-script-code', '

$.fn.exists = function(){return ($(this).length > 0);}
$.exists = function(selector) {return ($(selector).length > 0);}

$("#onewireDevices li span.onewireID")
    .css("color", "#BBB");

$("#executiveDevices li[data-onewire-id]").addClass("disabled");

$("#executiveDevices li span.valueOuter").click(function(){
    var li = $(this).closest("li");
    if(!li.hasClass("disabled")){
        if(client){
            var command = "{\"controllerID\":"+li.data("controller-id")+", \"command\":'.CMD_TYPE_READ.', \"deviceID\":"+li.data("device-id")+", \"value\":null}";
            //ws.send(command);
            client.send("/topic/domoWebProcess", {}, command);
            $("#log").prepend("<p>&gt;&gt;&gt; "+command+"</p>");
        }
    }
});

var onewirePins = '.json_encode($onewirePins).';
var onewireIDs_other = [];



WebSocketStompMock = SockJS;
var client = Stomp.client("https://"+window.location.hostname+":'.Yii::app()->params['amqp']['port'].'/stomp");

client.debug = function(m, p){
    $("#log").prepend("<p class=\"rabbitmq\">"+m+"</p>");
}

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
    

    for(var controllerID in onewirePins){
        for(var pin in onewirePins[controllerID]){
            var command = "{\"controllerID\":"+controllerID+", \"command\":'.CMD_TYPE_LIST.', \"deviceID\":"+onewirePins[controllerID][pin]+", \"value\":null}";
            client.send("/topic/domoWebProcess", {}, command);
        }
    }

//    client.send("/topic/domoprocess", {}, );

}, function(){
    //alert( "error" );
});


var parseRecieve = function(d){

    for(var ind in d.recieve){
        var rec = d.recieve[ind];
        //alert(rec["command"]);
        if(rec["command"] == "1WIRE_LIST"){
            var msg = "1 Wire list: <br/>";
            for(var vind in rec["values"]){
                $("#onewireDevices li span[data-controller-id="+rec["controllerID"]+"][data-pin="+rec["pin"]+"][data-onewire-id="+rec["values"][vind]+"]")
                    .css("color", "#0B0");
                //$("#executiveDevices li[] span.inner")
                $("#executiveDevices li[data-controller-id="+rec["controllerID"]+"][data-onewire-pin="+rec["pin"]+"][data-onewire-id="+rec["values"][vind]+"]").removeClass("disabled").addClass("enabled");
                if(!$("#onewireDevices li span[data-controller-id="+rec["controllerID"]+"][data-pin="+rec["pin"]+"][data-onewire-id="+rec["values"][vind]+"]").exists()){
                    onewireIDs_other.push([rec["controllerID"], rec["pin"], rec["values"][vind]]);
                }
                msg += " - "+rec["values"][vind]+"<br/>";
            }
            if(onewireIDs_other.length > 0){
                for(var i=0; i<onewireIDs_other.length; i++){
                    $("#onewireDevices_other li[data-controller-id="+onewireIDs_other[i][0]+"][data-pin="+onewireIDs_other[i][1]+"][data-onewire-id="+onewireIDs_other[i][2]+"]").remove();
                    $("#onewireDevices_other").append("<li data-controller-id=\""+onewireIDs_other[i][0]+"\" data-pin=\""+onewireIDs_other[i][1]+"\" data-onewire-id=\""+onewireIDs_other[i][2]+"\"><span>"+onewireIDs_other[i][2]+"</span></li>");
                }
                $("#onewireDevices_other_container").show();
            }
            alertify.success(msg);
        } else if(rec["command"] == "1WIRE_ADDR"){
            var msg = "1 Wire addr: ";

            $("#onewireDevices li span[data-controller-id="+rec["controllerID"]+"][data-pin="+rec["pin"]+"][data-onewire-id="+rec["value"]+"]")
                .css("color", "#0B0");
            //$("#executiveDevices li[] span.inner")
            $("#executiveDevices li[data-controller-id="+rec["controllerID"]+"][data-onewire-pin="+rec["pin"]+"][data-onewire-id="+rec["value"]+"]").removeClass("disabled").addClass("enabled");
            if(!$("#onewireDevices li span[data-controller-id="+rec["controllerID"]+"][data-pin="+rec["pin"]+"][data-onewire-id="+rec["value"]+"]").exists()){
                onewireIDs_other.push([rec["controllerID"], rec["pin"], rec["value"]]);
            }
            msg += rec["value"]+"<br/>";

            if(onewireIDs_other.length > 0){
                for(var i=0; i<onewireIDs_other.length; i++){
                    $("#onewireDevices_other li[data-controller-id="+onewireIDs_other[i][0]+"][data-pin="+onewireIDs_other[i][1]+"][data-onewire-id="+onewireIDs_other[i][2]+"]").remove();
                    $("#onewireDevices_other").append("<li data-controller-id=\""+onewireIDs_other[i][0]+"\" data-pin=\""+onewireIDs_other[i][1]+"\" data-onewire-id=\""+onewireIDs_other[i][2]+"\"><span>"+onewireIDs_other[i][2]+"</span></li>");
                }
                $("#onewireDevices_other_container").show();
            }

            alertify.success(msg);
        } else if(rec["command"] == "DEVICE_VALUE"){
            var value = rec["value"];
            //value = null;
            //alert((value == null || value == "") && value != 0 );
            if((value == null || value == "") && value != 0)
                value = "null";
            $("#executiveDevices li[data-device-id="+rec["ID"]+"] span.value").html(value);
            var msg = $("#executiveDevices li[data-device-id="+rec["ID"]+"] span.inner span.caption").text();
            msg += " : "+value;
            alertify.success(msg);
        }
    }


}





')
?>
