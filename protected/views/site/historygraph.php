<?
$this->layout = 'simple';

$categories = array();
$data = array();

foreach($historyModels as $historyModel){
    $categories[] = $historyModel->datechange;
    $data[] = floatval($historyModel->value);
}
?>

<?=CHtml::link("Все данные", array('/site/historydata', 'deviceID'=>$deviceModel->id))?>
<div class="clear"></div>

<div style="padding-right: 220px; position: relative; height: 400px; ">
    <div id="graph">
<? $this->widget('application.extensions.highcharts.HighchartsWidget', array(
    'options' => array(
        'title' => array('text'=>$deviceModel->caption),
        'plotOptions' => array('line'=>array('dataLabels'=>array('enabled'=>false), 'showInLegend'=>false)),
        'xAxis' => array(
            'categories' => $categories,
            'labels' => array(
                'rotation' => -90,
            ),
        ),
        'yAxis' => array(
            'title' => array('text'=>null),
        ),
        'series' => array(
            array('name'=>null, 'data'=>$data),
        ),
        'credits' => array('enabled'=>false),
    ),
)); ?>
    </div>

    <? ksort($historyModels); ?>
    <div id="data" style="width: 210px; position: absolute; top: 0; right: 0; height: 400px; overflow: auto; ">
        <? foreach($historyModels as $historyModel): ?>
            <?=$historyModel->datechange?>: <?=$historyModel->value?> <br/>
        <? endforeach; ?>
    </div>
    
</div>


<? Yii::app()->clientScript->registerCoreScript('jquery')
        ->registerScript('historygraph-script-code', '



')
?>
