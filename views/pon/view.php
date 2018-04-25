<?php

use miloschuman\highcharts\Highstock;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\helpers\Url;

$this->title = $model->interface . " (" . $model->host . ")";
$this->params['breadcrumbs'][] = ['label' => 'Уровни сигналов PON', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->interface;

if ($model->reason == 'включена') {
    $color['reason'] = ['class' => 'success'];
} elseif ($model->reason == 'питание') {
    $color['reason'] = ['class' => 'warning'];
} elseif ($model->reason == 'кабель') {
    $color['reason'] = ['class' => 'danger'];
} else {
    $color['reason'] = ['class' => 'info'];
}

if ($model->onu_power <= -28) {
    $color['onu_power'] = ['class' => 'danger'];
} elseif ( $model->onu_power == 0 ) {
    $color['onu_power'] = ['class' => 'warning'];
} else {
    $color['onu_power'] = ['class' => ''];
}

if ($model->olt_power <= -28) {
    $color['olt_power'] = ['class' => 'danger'];
} elseif ( $model->olt_power == 0 ) {
    $color['olt_power'] = ['class' => 'warning'];
} else {
    $color['olt_power'] = ['class' => ''];
}
?>

<div class="row" style="margin-bottom: 20px;">
    <div  class="col-xs-12" >
        <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
    </div>
</div>

<?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
<div class="row" style="margin-bottom: 20px;">
    <div class="col-xs-8">
        <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['index'], [
                'class' => 'btn btn-warning',
                'data-pjax' => 0,
            ]) ?>
        <?= Html::a('<span class="glyphicon glyphicon-search"></span>', ['view', 'id' => $model->mac], [
                'class' => 'btn btn-info',
                'title' => 'Обновить',
                'data-pjax' => 1,
            ]) ?>
        <?= Yii::$app->user->can('rebootPon') ? Html::a('<span class="glyphicon glyphicon-refresh"></span>', ['reboot', 'id' => $model->mac], [
                'class' => 'btn btn-danger',
                'title' => 'Перезагрузить',
                'data' => [
                    'confirm' => 'Вы действительно хотите перезагрузить ONU?',
                    'method' => 'post',
                ]
            ]) : '' ?>
    </div>

    <?php if ($model->reason != 'включена'): ?>
    <div class="col-xs-4" align="right">
        <?= Yii::$app->user->can('deletePon') ? Html::a('<span class="glyphicon glyphicon-fire"></span> Сгорела', ['delete', 'id' => $model->mac], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить все данные об ONU?',
                'method' => 'post',
            ],
        ]) : '' ?>
    </div>
    <?php endif; ?>
</div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'num',
                'label' => 'Договор',
                'format' => 'raw',
                'value' => function($model) {
                        $onuService = Yii::$app->params['onuService'];
                        return array_key_exists($model->mac, $onuService) ? '' : Html::a(Html::encode($model->client['num']), Url::to(['client/view', 'id' => $model->client['id'] ]), ['data-pjax' => 0]);
                    },
                'visible' => $model->client['num'] !== null,
            ],            
            [
                'attribute' => 'client.name',
                'value' => function($model) {
                        $onuService = Yii::$app->params['onuService'];
                        return array_key_exists($model->mac, $onuService) ? '' : $model->client['name'];
                    },
                'visible' => $model->client['name'] !== null,
            ],
            [
                'attribute' => 'client.street',
                'value' => function($model) {
                        $onuService = Yii::$app->params['onuService'];
                        return array_key_exists($model->mac, $onuService) ? $onuService[$model->mac] : $model->client->street . ', ' . $model->client->building . ', ' .$model->client->room;
                    },
                'label' => 'Адрес',
                'visible' => $model->client['street'] !== null,
            ],
            'interface',
            'mac',
            [
                'attribute' => 'olt_power',
                'contentOptions' => $color['olt_power'],
            ],
            [ 
                'attribute' => 'onu_power',
                'contentOptions' => $color['onu_power'],
            ],
            [
                'attribute' => 'distance',
                'label' => 'Расстояние',
                'value' => function($model) {
                        return $model->distance . ' м';
                },
            ],
            [
                'attribute' => 'reason',
                'contentOptions' => $color['reason'],
            ],
            'date',
        ],
    ]) ?>

<?php
echo Highstock::widget([
    'scripts' => [
        'themes/grid-light',
    ],
    'options' => [
        'global' => [
            'useUTC' => false,
        ],
        'legend' => [
            'enabled' => true,
        ],
        'rangeSelector' => [
            'buttons' => [
                [
                    'type' => 'hour',
                    'count' => 1,
                    'text' => '1 час'
                ], [
                    'type' => 'day',
                    'count' => 1,
                    'text' => '1 день'
                ], [
                    'type' => 'month',
                    'count' => 1,
                    'text' => '1 месяц'
                ], [
                    'type' => 'year',
                    'count' => 1,
                    'text' => '1 год'
                ], [
                    'type' => 'all',
                    'text' => 'Все'
                ]
            ],
            'selected' => 1,
            'buttonTheme' => [
               'width' => null,
               'padding' => 4
            ],
            'inputEnabled' => false
        ],
        'navigator' => [
            'yAxis' => [
                'reversed' => true,
            ],
        ],
        'credits' => [
            'enabled' => false
        ],
        'chart' => [
            'type' => 'spline',
            'zoomType' => 'x',
        ],
        'title' => [
            'text' => 'Уровень сигнала ' . $model->interface,
        ],
        'xAxis' => [
            'type' => 'datetime',
            'dateTimeLabelFormats' => [
                'minute' => " %H:%M",
                'hour' => "%H:%M",
                'day' => "<b>%d/%m/%Y</b>",
                'week' => "%d/%m/%Y",
                'month' => "%d/%m/%Y",
                'year' => "%Y"
            ],

//            'dateTimeLabelFormats' => [
//                'millisecond' => "%A, %b %e, %H:%M:%S.%L",
//                'second' => "%A, %b %e, %H:%M:%S",
//                'minute' => "%A, %b %e, %H:%M",
//                'hour' => "%A, %b %e, %H:%M",
//                'day' => "%A, %b %e, %Y",
//                'week' => "Week from %A, %b %e, %Y",
//                'month' => "%B %Y",
//                'year' => "%Y"
//            ],
        ],
        'yAxis' => [
            'reversed' => true,
            'title' => ['text' => 'дБм'],
            'opposite' => false,
            'plotLines' => [
                [
                    'value' => -28,
                    'color' => 'red',
                    'dashStyle' => 'shortdash',
                    'width' => 1,
                    'zIndex' => 100,
                    'label' => [
                        'text' => 'Уровень -28'
                    ]
                ]
            ]
        ],
        'tooltip' => [
            'headerFormat' => '<b>{point.x:%d/%m/%Y %H:%M}</b><br>',
            'pointFormat' => '{series.name}: <b>{point.y}</b> дБм<br>',
            'valueDecimals' => 2
        ],
        'plotOptions' => [
            'spline' => [
                'marker' => [
                    'enabled' => false
                ],
                'zones' => [
                    [
                        'value' => -28,
                        'color' => 'red',
                    ],
                ],
            ],
        ],
        'series' => [
            ['name' => 'уровень на OLT', 'data' => $result['olt_power'] ],
            ['name' => 'уровень на ONU', 'data' => $result['onu_power'] ],
        ]
    ]
]);

echo Highstock::widget([
    'scripts' => [
        'themes/grid-light',
    ],
    'options' => [
        'global' => [
            'useUTC' => false,
        ],
        'legend' => [
            'enabled' => true
        ],
        'rangeSelector' => [
            'buttons' => [
                [
                    'type' => 'hour',
                    'count' => 1,
                    'text' => '1 час'
                ], [
                    'type' => 'day',
                    'count' => 1,
                    'text' => '1 день'
                ], [
                    'type' => 'month',
                    'count' => 1,
                    'text' => '1 месяц'
                ], [
                    'type' => 'year',
                    'count' => 1,
                    'text' => '1 год'
                ], [
                    'type' => 'all',
                    'text' => 'Все'
                ]
            ],
            'selected' => 1,
            'buttonTheme' => [
               'width' => null,
               'padding' => 4
            ],
            'inputEnabled' => false
        ],
        'credits' => [
            'enabled' => false
        ],
        'chart' => [
            'type' => 'spline',
            'zoomType' => 'x',
        ],
        'title' => [
            'text' => 'Температура ONU',
        ],
        'xAxis' => [
            'type' => 'datetime',
            'dateTimeLabelFormats' => [
                'minute' => " %H:%M",
                'hour' => "%H:%M",
                'day' => "<b>%d/%m/%Y</b>",
                'week' => "%d/%m/%Y",
                'month' => "%d/%m/%Y",
                'year' => "%Y"
            ],
        ],
        'yAxis' => [
            'title' => ['text' => 'C'],
            'opposite' => false,
            'plotLines' => [
                [
                    'value' => 60,
                    'color' => 'red',
                    'dashStyle' => 'shortdash',
                    'width' => 1,
                    'zIndex' => 100
                ]
            ]
        ],
        'tooltip' => [
            'headerFormat' => '<b>{point.x:%d/%m/%Y %H:%M}</b><br>',
            'pointFormat' => '{series.name}: <b>{point.y}</b> C<br>',
        ],
        'plotOptions' => [
            'spline' => [
                'color' => 'green',
                'marker' => [
                    'enabled' => false
                ],
                'zones' => [
                    [
                        'value' => 60,
                    ],
                    [
                        'color' => 'red'
                    ]
                ],
            ],
        ],
        'series' => [
            ['name' => 'температура ONU', 'data' => $result['temperature_onu'] ],
        ]
    ]
]);
?>
<?php Pjax::end(); ?>

<?php
$script = <<< JS
    $(document).on('pjax:send', function() {
        $("html,body").css("overflow-x","hidden");
        $('#loading-indicator').removeClass("kv-hide");

    });
    $(document).on('pjax:complete', function() {
        $("html,body").css("overflow-x","auto");
        $('#loading-indicator').addClass("kv-hide");
    });

JS;
$this->registerJs($script);
?>


