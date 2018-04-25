<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Money */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Moneys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="money-view">
    <div  align="center">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <p>
    <div class="row">
        <div class="col-md-6">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
            <?= Html::a('<span class="glyphicon glyphicon-pencil"></span> Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="col-md-6" align='right'>
            <?= Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить данную запись?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'num',
        ],
    ]) ?>

</div>
