<?php

/* @var $this yii\web\View */

$this->title = 'Система учета пользователей';
?>
<div class="site-index">
    <div class="jumbotron row">
            <?php if (Yii::$app->user->can('createClient')): ?>
                <a class="btn btn-lg btn-success col-lg-4 col-xs-12" href="client/create">Добавить пользователя</a>
            <?php endif; ?>
            <a class="btn btn-lg btn-success col-lg-4 col-xs-12" href="client/index">Список пользователей</a>
            <a class="btn btn-lg btn-success col-lg-4 col-xs-12" href="inet/index">Список подключений</a>
            <?php if (Yii::$app->user->can('indexStat')): ?>
                <a class="btn btn-lg btn-success col-lg-4 col-xs-12" href="stat/index">Статистика</a>
            <?php endif; ?>
            <?php if (Yii::$app->user->can('indexStat')): ?>
                <a class="btn btn-lg btn-success col-lg-4 col-xs-12" href="log/index">Логи</a>
            <?php endif; ?>
    </div>
</div>
