<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->ip;
$width = 80 / (count($interfacesStatus) / 2) - 3;
?>

<div class="switches-view">
    <div class="row">
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

    <div class="row device">
        <div class="row">
            <?php $i = 1 ?>
            <?php foreach ($interfacesStatus as $id => $item): ?>
                <?php if ($i % 2 != 0): ?>
                    <?php $status = ($item == 1) ? 'active' : '' ?>
                    <div class="interface <?= $status ?>" style="width: <?= $width ?>%;"><?= $i ?></div>
                <?php endif; ?>
                <?php $i++ ?>
            <?php endforeach; ?>
        </div>
        <div class="row">
            <?php $i = 1 ?>
            <?php foreach ($interfacesStatus as $id => $item): ?>
                <?php if ($i % 2 == 0): ?>
                    <?php $color = ($item == 1) ? 'active' : '' ?>
                    <div class="interface <?= $status ?>" style="width: <?= $width ?>%;"><?= $i ?></div>
                <?php endif; ?>
                <?php $i++ ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
