<button onclick="$.lightsoffButtonOffClick(this)">Выключить</button>

<? Yii::app()->clientScript->registerScript(__FILE__, '
    
$.lightsoffButtonOffClick = function(elem){

    $.ajax({
        url: Yii.app.createUrl("/plugin/doaction", {id: $(elem).closest(".item").data("plugin-id"), action: "turn_off"}),
        dataType: "json",
        success: function(data){

        }, 
        error: function(){
            alert("error");
        }
    });

}

'); ?>