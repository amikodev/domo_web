<? $paramModel = Yii::app()->systemParam->get('total_security_state'); ?>
<? //var_dump($value); ?>
<? if($paramModel->value == 'on'): ?>
    Включено <br/>
    <button onclick="$.securityButtonOffClick(this)">Выключить</button>
<? elseif($paramModel->value == 'off'): ?>
    Выключено <br/>
    <button onclick="$.securityButtonOnClick(this)">Включить</button>
<? else: ?>
    Статус не определён <br/>
    <button onclick="$.securityButtonOnClick(this)">Включить</button>
<? endif; ?>

<? Yii::app()->clientScript->registerScript(__FILE__, '
    
$.securityButtonOnClick = function(elem){

    $.ajax({
        url: Yii.app.createUrl("/plugin/doaction", {id: $(elem).closest(".item").data("plugin-id"), action: "turn_on"}),
        dataType: "json",
        success: function(data){
            //alert(objInfo(data));
//            if(data["state"] == "on"){
//                alertify.success("Общая безопасность включена");
//            } else{
//                alertify.error("Ошибка включения общей безопасности");
//            }
            //$(elem).closest(".item").find(".content").load(Yii.app.createUrl("/plugin/renderItem", {id: $(elem).closest(".item").data("plugin-id")}));
        }, 
        error: function(){
            alert("error");
        }
    });

}

$.securityButtonOffClick = function(elem){

    $.ajax({
        url: Yii.app.createUrl("/plugin/doaction", {id: $(elem).closest(".item").data("plugin-id"), action: "turn_off"}),
        dataType: "json",
        success: function(data){
            //alert(objInfo(data));
//            if(data["state"] == "off"){
//                alertify.success("Общая безопасность выключена");
//            } else{
//                alertify.error("Ошибка выключения общей безопасности");
//            }
            //$(elem).closest(".item").find(".content").load(Yii.app.createUrl("/plugin/renderItem", {id: $(elem).closest(".item").data("plugin-id")}));
        }, 
        error: function(){
            alert("error");
        }
    });

}

'); ?>