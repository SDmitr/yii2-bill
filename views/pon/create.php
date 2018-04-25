<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Pon */

$this->title = 'Create Pon';
$this->params['breadcrumbs'][] = ['label' => 'Pons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pon-create">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', Yii::$app->request->referrer, ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-sm-10" >
            <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
