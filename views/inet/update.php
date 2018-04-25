<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Inet */

$this->title = 'Редактирование подключения: ' . $model->num;
$this->params['breadcrumbs'][] = ['label' => 'Интернет', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->num, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменение подключения';
?>
<div class="inet-update">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['view', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-xs-9 col-sm-10">
            <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
        'networks' => $networks,
        'networkId' => $networkId,
    ]) ?>

</div>
