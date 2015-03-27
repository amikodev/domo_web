<?php
/* @var $this ScenarioController */
/* @var $model Scenario */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'scenario-form',
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
		<?php echo $form->labelEx($model,'delay'); ?>
		<?php echo $form->textField($model,'delay'); ?>
		<?php echo $form->error($model,'delay'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'deviceID'); ?>
		<?php echo $form->dropDownList($model,'deviceID', array(''=>'')+CHtml::listData(Device::model()->findAll(array('order'=>'caption')), 'id', 'caption')); ?>
		<?php echo $form->error($model,'deviceID'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'actived'); ?>
		<?php echo $form->dropDownList($model,'actived', array(1=>'Да', 0=>'Нет')); ?>
		<?php echo $form->error($model,'actived'); ?>
	</div>

    <div id="editor"><?=CHtml::encode($model->content)?></div>
    
    <?php echo $form->hiddenField($model,'content'); ?>
    
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<? Yii::app()->clientScript
        ->registerScriptFile(Yii::app()->baseUrl.'/js/ace/src-min-noconflict/ace.js')
        ->registerCss('scenario-style-code', '

#editor{ 
    position: relative;
    width: 100%;
    height: 300px;
}


')
        ->registerScript('scenario-script-code', '


var editor = ace.edit("editor");
editor.setTheme("ace/theme/tomorrow");
editor.getSession().setMode("ace/mode/php");
//editor.setReadOnly(true);


$("form#scenario-form").submit(function(){

    $("#Scenario_content").val(editor.getValue());

});


')
?>
