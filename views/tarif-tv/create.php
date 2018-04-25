<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TarifTv */

$this->title = 'Создание тарифа IPTv';
$this->params['breadcrumbs'][] = ['label' => 'Тарифы IPTv', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tarif-tv-create">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['index'], ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-xs-9 col-sm-10">
            <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
