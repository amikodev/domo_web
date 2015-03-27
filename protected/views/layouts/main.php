<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" />

    <link id="favicon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/domo_ico.png" type="image/x-icon" rel="shortcut icon">    
    
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
        
        <? $certEnd = strtotime($_SERVER['SSL_CLIENT_V_END']); ?>
        <? $certCN = $_SERVER["SSL_CLIENT_S_DN_CN"]; ?>
        <div id="certificateInfo">
            <div class="CN">
                Сертификат: <span><?=$certCN?></span>
            </div>
            <div class="dateEnd">
                Окончание: <span><?=date("d.m.Y H:i:s", $certEnd)?></span>, 
                осталось [<span><?=intval(($certEnd-time())/(24*60*60))?></span>]
            </div>
            
        </div>
		<div id="logo">
            <?=CHtml::link('Домовой', array('/site/index'))?>
        </div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
                
				array('label'=>'Главная', 'url'=>array('/site/scene')),
				array('label'=>'Инфо', 'url'=>array('/site/devices')),
                
				array('label'=>'Сцены', 'url'=>array('/scene')),
				array('label'=>'Устройства', 'url'=>array('/device')),
				array('label'=>'Сценарии', 'url'=>array('/scenario')),
				array('label'=>'Плагины', 'url'=>array('/plugin')),
				array('label'=>'Системные параметры', 'url'=>array('/systemparam')),
                
                
                
//				//array('label'=>'Главная', 'url'=>array('/site/index')),
//				array('label'=>'Главная', 'url'=>array('/site/scene')),
//				array('label'=>'Устройства', 'url'=>array('/device')),
//				array('label'=>'Инфо', 'url'=>array('/site/devices')),
//				array('label'=>'Сценарии', 'url'=>array('/scenario')),
//				array('label'=>'Плагины', 'url'=>array('/plugin')),
//				//array('label'=>'Музыка', 'url'=>array('/music')),
//				//array('label'=>'СМС', 'url'=>array('/sms')),
//				array('label'=>'Системные параметры', 'url'=>array('/systemparam')),
			),
		)); ?>
	</div><!-- mainmenu -->
    
    
    
	<?php if(isset($this->breadcrumbs)):?>
        <br/>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

</div><!-- page -->

<div id="footer">
    <p>Домашняя автоматизация "Домовой"</p>
    <p><?=date('Y')>2013?'2013 - ':''?><?=date('Y')?> &copy; Amikodev </p>
</div><!-- footer -->

</body>
</html>

<? Yii::app()->EJSUrlManager->init(); ?>

<? Yii::app()->clientScript->registerCoreScript('jquery')
        ->registerCoreScript('jquery.ui')
        
        ->registerScriptFile(Yii::app()->request->baseUrl.'/js/alertify/lib/alertify.min.js')
        ->registerCssFile(Yii::app()->request->baseUrl.'/js/alertify/themes/alertify.core.css')
        ->registerCssFile(Yii::app()->request->baseUrl.'/js/alertify/themes/alertify.default.css')
        
        ->registerScriptFile(Yii::app()->request->baseUrl.'/js/main.js')
        
        ->registerScript('main-layout-script-code', '
  

var certEnd = '.$certEnd.';
var nowTime = '.time().';

if(certEnd-nowTime < 30*24*60*60){
    $("#certificateInfo .dateEnd span").addClass("notify");
} else if(certEnd-nowTime < 14*24*60*60){
    $("#certificateInfo .dateEnd span").addClass("alert");
    var count = Math.floor((certEnd-nowTime)/(24*60*60));
    alertify.error("Заканчивается срок действия сертификата. \nОсталось "+count+" дней.");
}

')
?>


<? // миниминизация меню для iPad ?>
<? if(strpos($_SERVER["HTTP_USER_AGENT"], 'iPad') !== false): ?>

<? Yii::app()->clientScript

        ->registerCssFile(Yii::app()->baseUrl.'/js/jqwidgets/styles/jqx.base.css')
        
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jqwidgets/jqxcore.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jqwidgets/jqxbuttons.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jqwidgets/jqxscrollbar.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jqwidgets/jqxcheckbox.js')
        ->registerScriptFile(Yii::app()->baseUrl.'/js/jqwidgets/jqxmenu.js')
        
        ->registerScript('scene-jqxwidgets-script-code', '


//$("#mainmenu").jqxMenu({ width: "100%", height: "32px", autoSizeMainItems: true});
$("#mainmenu").jqxMenu({ autoSizeMainItems: true});
$("#mainmenu").jqxMenu("minimize");

$("#mainmenu")
    .css("background-image", "url()")
    .css("border", "0")
    .css("float", "left")
    .css("width", "100px")
    ;
    
$("#header")
    .css("float", "right")
    .css("border", "0")
    ;



');        
        
?>

<? endif; ?>


