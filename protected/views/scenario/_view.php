<?php
/* @var $this ScenarioController */
/* @var $data Scenario */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('caption')); ?>:</b>
	<?php echo CHtml::encode($data->caption); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('delay')); ?>:</b>
	<?php echo CHtml::encode($data->delay); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('deviceID')); ?>:</b>
	<?php echo CHtml::encode($data->deviceID); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('scenarioComponent')); ?>:</b>
	<?php echo CHtml::encode($data->scenarioComponent); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('content')); ?>:</b>
	<?php echo CHtml::encode($data->content); ?>
	<br />


</div>