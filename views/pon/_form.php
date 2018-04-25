<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Pon */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pon-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'mac')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'interface')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'olt_power')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'onu_power')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transmitted_power')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'temperature_onu')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'distance')->textInput() ?>

    <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
