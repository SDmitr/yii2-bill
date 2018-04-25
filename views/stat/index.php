<?php

use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\Highmaps;
use yii\web\JsExpression;
use yii\helpers\Html;

$this->title = 'Статистика';
?>
<div  align="center">
    <h3>Количество подключений <?= Html::encode($summary) ?></h3>
</div>
<div class="row">
    <div id="chart_1" class="col-lg-6"></div>
    <div id="chart_2" class="col-lg-6"></div>
</div>
<?php
echo Highcharts::widget([
    'scripts' => [
        'modules/exporting',
        'themes/grid-light',
        'highcharts-3d',
    ],
    'options' => [
        'credits' => [
          'enabled' => false
        ],
        'chart' => [
            'type' => 'pie',
            'options3d' => [
                'enabled' => true,
                'alpha' => 50
            ],
            'renderTo' => 'chart_1',
            'events' => [
                'drilldown' => new JsExpression("function(e) {this.setTitle({ text: e.point.name });}"),
                'drillup' => new JsExpression("function(e) {this.setTitle({ text: 'Распределение пользователей по тарифам' });}"),
            ],
        ],
        'lang' => [
            'drillUpText' => '<< Вернуться к общей диаграмме',
        ],
        'tooltip' => [
            'enabled' => true,
            'useHTML' => true,
            'delayForDisplay' => 1,
            'hideDelay' => 1,
            'borderWidth' => 0,
            ],
        'plotOptions' => [
            'pie' => [
                'innerSize' => '50%',
                'depth' => 50,
                'allowPointSelect' => true,
                'cursor' => 'pointer',
                'slicedOffset' => 20,
            ],
        ],
        'title' => [
            'text' => 'Распределение пользователей по тарифам',
        ],
        'series' => [
            [
                'colorByPoint' => true,
                'name' => 'Количество пользователей',
                'data' => $tarifs[0],
            ],
        ],
        'drilldown' => [
            'drillUpButton' => [
                'position' => [
                    'align' => 'center',
                ],
            ],
            'series' => $tarifs[1],
        ]
    ],
    
]);

echo Highcharts::widget([
    'scripts' => [
        'modules/drilldown',
        'modules/exporting',
        'themes/grid-light',
        'highcharts-3d',
    ],
    'options' => [
        'credits' => [
          'enabled' => false
        ],
        'chart' => [
            'type' => 'pie',
            'options3d' => [
                'enabled' => true,
                'alpha' => 50
            ],
            'renderTo' => 'chart_2',
            'events' => [
                'drilldown' => new JsExpression("function(e) {this.setTitle({ text: e.point.name });}"),
                'drillup' => new JsExpression("function(e) {this.setTitle({ text: 'Распределение пользователей по локациям' });}"),
            ]
        ],
        'lang' => [
            'drillUpText' => '<< Вернуться к общей диаграмме',
        ],
        'tooltip' => [
            'enabled' => true,
            'useHTML' => true,
            'delayForDisplay' => 1,
            'hideDelay' => 1,
            'borderWidth' => 0,
            ],
        'plotOptions' => [
            'pie' => [
                'innerSize' => '50%',
                'depth' => 50,
                'allowPointSelect' => true,
                'cursor' => 'pointer',
                'slicedOffset' => 20,
            ],
        ],
        'title' => [
            'text' => 'Распределение пользователей по локациям',
        ],
        'series' => [
            [
                'name' => 'Количество пользователей',
                'data' => $networks[0],
            ],
        ],
        'drilldown' => [
            'drillUpButton' => [
                'position' => [
                    'align' => 'center',
                ],
            ],
            'series' => $networks[1],
        ],
    ]
]);

?>

