<?php
use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['stat/index'], ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-xs-9 col-sm-10">
            <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    

    <div class="alert alert-success">
        <?= nl2br(Html::encode($message)) ?>
    </div>
    <div class="alert alert-info pre-scrollable">
        <?= nl2br(Html::encode($config)) ?>
    </div>
</div>


