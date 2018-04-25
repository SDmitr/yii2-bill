<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Pon */

$this->title = 'Update Pon: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Pons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="pon-update">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-lg-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-lg-10" >
            <h2 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h2>
        </div>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
