<?php
/* @var $this ScenarioController */
/* @var $model Scenario */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'caption'); ?>
		<?php echo $form->textField($model,'caption',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'delay'); ?>
		<?php echo $form->textField($model,'delay'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'deviceID'); ?>
		<?php echo $form->textField($model,'deviceID'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'scenarioComponent'); ?>
		<?php echo $form->textField($model,'scenarioComponent',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->