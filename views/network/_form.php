<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Network */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="network-form">
    <?php $form = ActiveForm::begin([
    ]); ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <?= $form->field($model, 'subnet')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'mask')->dropDownList(Yii::$app->params['mask'], [
                    'prompt' => 'Выберите префикс',
                'labelOptions' => true,
                    'options' => [
                        $model->mask => [
                            'Selected' => true,
                            ],
                        ],
                    ]
                )->label('Префикс') ?>
        </div>    
        <div class="col-md-5">    
            <label class="control-label" for="network-mask-text">Маска</label>
            <input type="text" id="network-mask-text" class="form-control" readonly="true">
        </div>
    </div>
    <div class="row">       
        <div class="col-md-4">
            <?= $form->field($model, 'first_ip')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'last_ip')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'gateway')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        </div>
    </div>
    <div class="row">       
        <div class="col-md-6">
            <?= $form->field($model, 'dns1')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'dns2')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row-1">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success col-xs-12' : 'btn btn-primary col-xs-12']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
    $this->registerJsFile('@web/js/get-network.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
