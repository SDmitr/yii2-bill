<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use kartik\spinner\Spinner;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">   
    
<?php
if (!Yii::$app->user->isGuest):
    NavBar::begin([
        'brandLabel' => '<span class="glyphicon glyphicon-home"></span> Главная',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
            'visible' => !Yii::$app->user->isGuest
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'encodeLabels' => false,
        'items' => [
            Yii::$app->user->can('createClient') ? (
                ['label' => '<span class="glyphicon glyphicon-user"></span> Добавить пользователя', 'url' => ['/client/create']]
            ) : '',
            ['label' => '<span class="glyphicon glyphicon-th-list"></span> Пользователи', 'url' => ['/client/index']],
            ['label' => '<span class="glyphicon glyphicon-globe"></span> Интернет', 'url' => ['/inet/index']],
            ['label' => '<span class="glyphicon glyphicon-cog"></span> Сервисы',
                'items' => [
                    ['label' => '<span class="glyphicon glyphicon-hdd"></span> Коммутаторы', 'url' => ['/switches/index']],
                    ['label' => '<span class="glyphicon glyphicon-flash"></span> Уровни сигналов PON', 'url' => ['/pon/index']],
                    Yii::$app->user->can('indexStat') ? (
                        ['label' => '<span class="glyphicon glyphicon-signal"></span> Статистика', 'url' => ['/stat/index']]
                    ) : '',
                    ['label' => '<span class="glyphicon glyphicon-refresh"></span> Перезагрузка DHCP', 'url' => ['/dhcp/create']],
//                    ['label' => '<span class="glyphicon glyphicon-facetime-video"></span> Список каналов', 'url' => ['/playlist/index']],
                    ['label' => '<span class="glyphicon glyphicon-envelope"></span> Техподдержка', 'url' => ['/site/contact']],
                ],
            ],
            Yii::$app->user->can('director') ? (
            ['label' => '<span class="glyphicon glyphicon-wrench"></span> Настройки',
                'items' => [
                    ['label' => '<span class="glyphicon glyphicon-road"></span> Улицы', 'url' => ['/street/index']],
                    ['label' => '<span class="glyphicon glyphicon-picture"></span> Локации', 'url' => ['/network/index']],
                    Yii::$app->user->can('createTarifInet') ? (
                        ['label' => '<span class="glyphicon glyphicon-usd"></span> Тарифы Интернет', 'url' => ['/tarif-inet/index']]
                    ) : '',
//                    Yii::$app->user->can('createTarifInet') ? (
//                        ['label' => '<span class="glyphicon glyphicon-eur"></span> Тарифы IPTv', 'url' => ['/tarif-tv/index']]
//                    ) : '',
                ],
            ]
            ) : '',
            Yii::$app->user->can('indexStat') ? (
                        ['label' => '<span class="glyphicon glyphicon-alert"></span> Логи', 'url' => ['/log/index']]
                    ) : '',
            Yii::$app->user->isGuest ? (
                ['label' => '<span class="glyphicon glyphicon-log-in"></span> Войти', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    '<span class="glyphicon glyphicon-log-out"></span> Выйти (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    endif;
?>
    
    <div class="container">
    
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>

</div>
    
<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->params['name'] ?> <?= date('Y') ?></p>
        <p class="pull-right">Powered by SDmitriy</p>
        <!--<p class="pull-right"><?= Yii::powered() ?></p>-->
    </div>
</footer>
    
<?= Spinner::widget([
    'options' => [
        'id' => 'loading-indicator',
    ],
    'hidden' => true,
    'pluginOptions' => [
        'color' => '#fff',
        'shadow' => true,
        'trail' => 60,
        'zIndex' => 2e9,
        'radius' => 42,
        'length' => 0,
        'width' => 20,
        'top' => '50%',
        'left' => '50%',
        'opacity' => 0,
        'position' => 'fixed',
        'speed' => 2,
        'lines' => 12,
    ]
]);
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
