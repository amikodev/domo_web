<?php
/* @var $this SceneController */
/* @var $model Scene */

$this->breadcrumbs=array(
	'Сцены',
);

$this->menu=array(
	array('label'=>'Создать сцену', 'url'=>array('create')),
);

?>

<h1>Сцены</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'scene-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
        array(
            'name' => 'id',
            'htmlOptions' => array(
                'width' => 80,
            ),
        ),
		'caption',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
