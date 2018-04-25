<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tv */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tv-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inet_id')->textInput() ?>

    <?= $form->field($model, 'tarif_id')->textInput() ?>

    <?= $form->field($model, 'status_id')->textInput() ?>

    <?= $form->field($model, 'date_on')->textInput() ?>

    <?= $form->field($model, 'date_off')->textInput() ?>

    <?= $form->field($model, 'date_create')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
