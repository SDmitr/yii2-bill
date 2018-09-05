<?php

use app\models\Network;
use app\models\Status;
use app\models\TarifInet;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Inet */
/* @var $form yii\widgets\ActiveForm */
//var_dump($networkId);
?>

<div class="inet-form">
    <?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
    <?php $form = ActiveForm::begin([]); ?>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'num')->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>
        </div>
        <?php if($model->isNewRecord): ?>
            <div class="col-md-6">
                <?= $form->field($networks, 'name')->dropDownList(Network::find()->select(['name', 'id'])->indexBy('id')->orderBy('name')->column(), [
                    'prompt' => 'Выберите локацию',
                    'options' => [
                         $networks->name => [
                            'selected' => true,
                            ]
                        ] 
                    ]
                ) ?>
            </div>
        <?php endif; ?>
        <?php if(!$model->isNewRecord): ?>
            <div class="col-md-6">
                <?= $form->field($networks, 'name')->dropDownList(Network::find()->select(['name', 'id'])->indexBy('id')->orderBy('name')->column(), [
                    'prompt' => 'Выберите локацию',
                    'options' => [
                         $networkId => [
                            'selected' => true,
                            ]
                        ] 
                    ]
                ) ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'ip')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'mac')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'tarif_id')->dropDownList(TarifInet::find()->select(['name', 'id'])->indexBy('id')->column()) ?>
        </div>
        <div class="col-md-6">
            <?php if($model->isNewRecord || Yii::$app->user->can('statusInet')): ?>
                <?= $form->field($model, 'status_id')->dropDownList(Status::find()->select(['name', 'id'])->indexBy('id')->column()) ?>
            <?php else : ?>
                <?= $form->field($model, 'status_id')->dropDownList(Status::find()->select(['name', 'id'])->indexBy('id')->column(), ['disabled' => true] ) ?>
            <?php endif; ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'switch')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'interface')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'onu_mac')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'date_create')->textInput(['class' => 'form-control ', 'readonly' => true]) ?>
        </div>
    </div>
    <?= $form->field($model, 'aton', ['template' => "{input}"])->hiddenInput(['readonly' => true])->label(false) ?>
    
    <div class="row-1">
        <?= Html::submitButton($model->isNewRecord ? 'Создать подключение' : 'Изменить подключение', ['class' => $model->isNewRecord ? 'btn btn-success col-xs-12' : 'btn btn-primary col-xs-12']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>


<?php
    $this->registerJsFile('@web/js/get-ip.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
