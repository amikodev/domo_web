<?
$this->layout = 'simple';
$models = $deviceModel->history;
krsort($models);
?>

<h2><?=CHtml::encode($deviceModel->caption)?></h2>

<? foreach($models as $historyModel): ?>
    <?=$historyModel->datechange?>: <?=$historyModel->value?> <br/>
<? endforeach; ?>
