<?php
/* @var $this PluginController */
/* @var $model Plugin */
/* @var $form CActiveForm */

$pluginNames = array();
foreach(glob(Yii::app()->basePath.'/components/plugins/*.php') as $fname){
    $name = str_replace(".php", "", basename($fname));
    $pluginNames[$name] = $name;
}

?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'plugin-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->dropDownList($model,'name', $pluginNames); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'caption'); ?>
		<?php echo $form->textField($model,'caption',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'caption'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'actived'); ?>
		<?php echo $form->dropDownList($model,'actived', array(1=>'Да', 0=>'Нет')); ?>
		<?php echo $form->error($model,'actived'); ?>
	</div>

    <? if(!$model->isNewRecord): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'params'); ?>
		<?php echo $form->textArea($model,'params',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'params'); ?>
	</div>
    <? endif; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->