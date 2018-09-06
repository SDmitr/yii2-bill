<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->ip;
$width = 80 / ($model->interface_count / 2) - 3;

var_dump($interfaces);

?>

<div class="street-view">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['index'], ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-xs-9 col-sm-10">
            <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'vendor',
            'interface_count'
        ],
    ]) ?>

    <div class="row" style="width: 75vw; height: 7vw; background-color: #546873; margin-right: unset; margin-left: unset; font-size: 1vw;">
        <div class="row" style="margin-right: unset; margin-left: unset; height: 30%; margin: 1vw;">
            <?php $i = 1 ?>
            <?php foreach ($interfaces as $id => $item): ?>
                <?php if ($i % 2 != 0): ?>
                    <?php $color = ($item['status'] == 1) ? '#25ec25' : '#b6c6b6' ?>
                    <div style="background-color: <?= $color ?>; width: <?= $width ?>%; margin-right: 1vw; float: left; text-align: center; height: 100%;"><?= $i ?></div>
                <?php endif; ?>
                <?php $i++ ?>
            <?php endforeach; ?>
        </div>
        <div class="row" style="margin-right: unset; margin-left: unset; height: 30%; margin: 1vw">
            <?php $i = 1 ?>
            <?php foreach ($interfaces as $id => $item): ?>
                <?php if ($i % 2 == 0): ?>
                    <?php $color = ($item['status'] == 1) ? '#25ec25' : '#b6c6b6' ?>
                    <div style="background-color: <?= $color ?>; width: <?= $width ?>%; margin-right: 1vw; float: left; text-align: center; height: 100%;"><?= $i ?></div>
                <?php endif; ?>
                <?php $i++ ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
