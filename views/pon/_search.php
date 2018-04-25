<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\PonSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pon-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'mac') ?>

    <?= $form->field($model, 'host') ?>

    <?= $form->field($model, 'interface') ?>

    <?= $form->field($model, 'olt_power') ?>

    <?php // echo $form->field($model, 'onu_power') ?>

    <?php // echo $form->field($model, 'transmitted_power') ?>

    <?php // echo $form->field($model, 'temperature_onu') ?>

    <?php // echo $form->field($model, 'distance') ?>

    <?php // echo $form->field($model, 'reason') ?>

    <?php // echo $form->field($model, 'date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
