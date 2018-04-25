<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TarifInet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tarif-inet-form">
    <div class="row">
        <?php $form = ActiveForm::begin(); ?>
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'speed')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'money')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row-1">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить тариф' : 'Изменить тариф', ['class' => $model->isNewRecord ? 'btn btn-success col-xs-12' : 'btn btn-primary col-xs-12']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
