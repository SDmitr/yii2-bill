<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tv */

$this->title = 'Create Tv';
$this->params['breadcrumbs'][] = ['label' => 'Tvs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tv-create">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['inet/view', 'id' => $model->inet->id], ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-xs-9 col-sm-10">
            <h2 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h2>
        </div>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
