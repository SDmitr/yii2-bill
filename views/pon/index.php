<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\PonLast;
use nterms\pagesize\PageSize;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Уровни сигналов PON';
//$this->params['breadcrumbs'][] = $this->title;

$reasonArray = PonLast::find()->select(['reason', 'reason'])->groupBy('reason')->column();
$reason = ArrayHelper::index($reasonArray, function ( $element )  { return $element;});

$olt = ArrayHelper::map(Yii::$app->params['OLT'], 'name', 'name' );

$pageSize = PageSize::widget([
                        'label' => 'Показать',
                        'template' => '{label} {list}',
                        'sizes' => [ 
                            1000000 => 'Все',
                            10 => '10',
                            50 => '50',
                        ],
                        'defaultPageSize' => 1000000,
                        'options' => [
                            'class' => 'form-control',
                            'style' => [
                                'display' => 'inline-block',
                                'width' => 'auto',
                                'vertical-align' => 'middle'
                            ]
                        ]
                    ]);
?>
<div class="pon-index">
<?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
//        'responsive' => false,
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',   
            ],
            [
                'attribute' => 'num',
                'label' => 'Договор',
                'value' => function($model) {
                        $onuService = Yii::$app->params['onuService'];
                        $num = array_key_exists($model->mac, $onuService) ? '' : $model->client['num'];
                        return $num;
                    },
            ],           
            [
                'attribute' => 'name',
                'label' => 'ФИО',
                'value' => function($model) {
                        $onuService = Yii::$app->params['onuService'];
                        $name = array_key_exists($model->mac, $onuService) ? $onuService[$model->mac] : $model->client['name'];
                        return $name;
                    },
            ],
            [
                'attribute' => 'street',
                'label' => 'Улица',
                'value' => function($model) {
                        return $model->client['street'];
                },
            ],
            [
                'attribute' => 'building',
                'label' => 'Дом',
                'value' => function($model) {
                        return $model->client['building'];
                },
            ],
            [
                'attribute' => 'room',
                'label' => 'Квартира',
                'value' => function($model) {
                        return $model->client['room'];
                },
            ],
            [
                'attribute' => 'host',
                'filter' => $olt,
                'filterInputOptions' => [
                    'id' => NULL,
                    'class' => 'form-control',
                    'prompt' => 'Все'
                ],
                'contentOptions' => [
                    'class' => 'text-nowrap',
                    'style' => 'width: 100px;'
                ]
            ],
            'interface',
            'mac',
//            'host',
            
            [ 
                'attribute' => 'olt_power',
                'contentOptions' => function($model) {
                    if ($model->olt_power <= -28) {
                        return ['class' => 'danger'];

                    } elseif ( $model->olt_power == 0 ) {
                        return ['class' => 'warning'];
                    } else {
                        return ['class' => ''];
                    }
                },
            ],
            [ 
                'attribute' => 'onu_power',
                'contentOptions' => function($model) {
                    if ($model->onu_power <= -28) {
                        return ['class' => 'danger'];

                    } elseif ( $model->onu_power == 0 ) {
                        return ['class' => 'warning'];
                    } else {
                        return ['class' => ''];
                    }
                },
            ],
//            'transmitted_power',
//            'temperature_onu',
            [
                'attribute' => 'distance',
                'label' => 'Расстояние',
                'value' => function($model) {
                        return $model->distance . ' м';
                },
            ],
            [
                'attribute' => 'reason',
                'filter' => $reason,
                'filterInputOptions' => [
                    'id' => NULL,
                    'class' => 'form-control',
                    'prompt' => 'Все'
                ],
                'contentOptions' => function($model) {
                    if ($model->reason == 'включена') {
                        return ['class' => 'success', 'style' => 'width: 130px'];
                    } elseif ($model->reason == 'питание') {
                        return ['class' => 'warning', 'style' => 'width: 130px'];
                    } elseif ($model->reason == 'кабель') {
                        return ['class' => 'danger', 'style' => 'width: 130px'];
                    } else {
                        return ['class' => 'info', 'style' => 'width: 130px'];
                    }
                },     
            ],
            'date',


        ],
        'layout' => "<div class='row'><div align='left' class='col-xs-6 form-inline'>" . $pageSize .  "</div><div align='right' class='col-xs-6'>{summary}</div></div><p>{items}<div align='center'>{pager}</div>"
    ]); ?>
<?php Pjax::end(); ?>
</div>
