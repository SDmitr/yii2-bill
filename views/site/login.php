<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Авторизация';
?>
<div class="site-login col-md-4 col-md-offset-4">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
//            'template' => "{label}<div class=\"col-lg-3\">{input}</div><div class=\"col-lg-8\">{error}</div>",
//            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'class' => 'form-control text-center'])->label('Логин') ?>
    
        <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control text-center'])->label('Пароль') ?>
        <?php // $form->field($model, 'rememberMe')->checkbox([
//            'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
//        ]) ?>

        <div class="form-group">
                 <?= Html::submitButton('<span class="glyphicon glyphicon-log-in"> Войти', ['class' => 'btn btn-success form-control', 'name' => 'login-button']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
