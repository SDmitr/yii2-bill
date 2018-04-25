<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Network */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Локации', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="network-view">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['index'], ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-xs-9 col-sm-10">
            <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    <p>
    <div class="row">
        <div class="col-xs-6 col-sm-8">
            <?= Yii::$app->user->can('updateNetwork') ? Html::a('<span class="glyphicon glyphicon-pencil"></span> Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : '' ?>
        </div>
        <div class="col-xs-6 col-sm-4" align="right">
            <?= Yii::$app->user->can('deleteNetwork') ? Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить данную локацию?',
                    'method' => 'post',
                ],
            ]) : '' ?>
        </div>
    </div>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'first_ip',
            'last_ip',
            'subnet',
            'mask',
            'gateway',
            'dns1',
            'dns2',
        ],
    ]) ?>

</div>
