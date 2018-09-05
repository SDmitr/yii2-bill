<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\TarifTv;
use app\models\Status;

/* @var $this yii\web\View */
/* @var $model app\models\Tv */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tv-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'tarif_id')->dropDownList(TarifTv::find()->select(['name', 'id'])->indexBy('id')->column()) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'status_id')->dropDownList(Status::find()->select(['name', 'id'])->indexBy('id')->column()) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'date_create')->textInput(['class' => 'form-control ', 'readonly' => true]) ?>
        </div>
    </div>
    <div class="row-1">
        <?= Html::submitButton($model->isNewRecord ? 'Создать подключение' : 'Изменить подключение', ['class' => $model->isNewRecord ? 'btn btn-success col-xs-12' : 'btn btn-primary col-xs-12']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
