<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Money */

$this->title = 'Update Money: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Moneys', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="money-update">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-xs-9 col-sm-10">
            <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
