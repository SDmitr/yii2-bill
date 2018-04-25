<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\TvSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tv-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'inet_id') ?>

    <?= $form->field($model, 'tarif_id') ?>

    <?= $form->field($model, 'status_id') ?>

    <?= $form->field($model, 'date_on') ?>

    <?php // echo $form->field($model, 'date_off') ?>

    <?php // echo $form->field($model, 'date_create') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
