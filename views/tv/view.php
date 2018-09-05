<?php

use app\models\TarifTv;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tv */

$this->title = $model->inet->ip;
$this->params['breadcrumbs'][] = ['label' => 'IPTv', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tv-view">
    <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
    <p>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-6">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['index'], ['class' => 'btn btn-warning']) ?>
        </div>
        <div class="col-xs-6" align="right">
            <?= Html::a('<span class="glyphicon glyphicon-user"></span> Пользователь', ['client/view', 'id' => $model->inet->client->id], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <p>
    <div class="row">
        <div class="col-xs-6">
            <?= Yii::$app->user->can('updateClient') ? Html::a('<span class="glyphicon glyphicon-pencil"></span> Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : '' ?>
        </div>
        <div class="col-xs-6" align="right">
            <?= Yii::$app->user->can('deleteClient') ? Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить данное подключение?',
                    'method' => 'post',
                ],
            ]) : '' ?>
        </div>
    </div>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'tarif_id',
                'value' => $model->tarif->name,
            ],
            [
                'attribute' => 'status_id',
                'value' => $model->status->name,
            ],
            'date_create',
        ],
    ]) ?>

</div>
