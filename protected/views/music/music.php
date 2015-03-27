<button id="start">Start</button> <br/>
<form>
    Громкость: <input type="text" name="volume" value="<?=Yii::app()->music->getVolume()?>"/> %
    <input id="setvolume" type="submit" value="Установить"/>
</form>


<? Yii::app()->clientScript->registerCoreScript('jquery')
        ->registerScript('music-script-code', '

$("button#start").click(function(){
    $.ajax({
        url: "'.Yii::app()->createUrl('/music/index', array('start'=>1)).'",
        success: function(data){
            //alert(data);
        },
        error: function(){
            alert("error");
        },
        complete: function(){
        }
    });
    return false;
});

//$("input#setvolume").click(function(){
$("form").submit(function(){
    $.ajax({
        url: "'.Yii::app()->createUrl('/music/index').'&volume="+$("input[name=volume]").val(),
        success: function(data){
            //alert(data);
        },
        error: function(){
            alert("error");
        },
        complete: function(){
        }
    });
    return false;
});


'); ?>