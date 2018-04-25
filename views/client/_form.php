<?php
use app\models\Street;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\AutoComplete;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">
    <?php $form = ActiveForm::begin([
    ]); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'num')->textInput() ?>
        </div>
        <div class="col-md-10">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'street')->widget(
                AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => Street::find()->select(['name as value', 'name as label'])->asArray()->all(),
                        'autoFocus' => true,
                    ],
                    'clientEvents' => [
                        'change' => 'function(event, ui) { if (ui.item==null) { $("#client-street").val(""); $("#client-street").focus(); }}'
                    ],
                    'options' => [
                        'class' => 'form-control',
                    ]
                ]
            ) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'building')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'room')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'phone_1')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'phone_2')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row-1">
        <?= Html::submitButton($model->isNewRecord ? 'Создать пользователя' : 'Изменить пользователя', ['class' => $model->isNewRecord ? 'btn btn-success col-xs-12' : 'btn btn-primary col-xs-12']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
