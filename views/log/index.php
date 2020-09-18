<?php

use app\models\Client;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use nterms\pagesize\PageSize;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\site\Log;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Логи';
$this->params['breadcrumbs'][] = $this->title;

$actionArray = Log::find()->select(['action', 'action'])->groupBy('action')->column();
$action = ArrayHelper::index($actionArray, function ( $element )  { return $element;});

$descriptionArray = Log::find()->select(['description', 'description'])->groupBy('description')->column();
$description = ArrayHelper::index($descriptionArray, function ( $element )  { return $element;});

$pageSize = PageSize::widget([
                        'label' => 'Показать',
                        'template' => '{label} {list}',
                        'sizes' => [ 
                            1000000 => 'Все',
                            10 => '10',
                            50 => '50',
                        ],
                        'defaultPageSize' => 10,
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
<style>
    hr {
        margin: 5px;
    }
</style>
<div class="log-index">
    <?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterSelector' => 'select[name="per-page"]',
            'rowOptions' => function($model) {
                switch ($model->level) {
                    case Log::INFO :
                        $rowColor = ['class' => 'info'];
                        break;
                    case Log::WARNING :
                        $rowColor = ['class' => 'warning'];
                        break;
                    case Log::DANGER :
                        $rowColor = ['class' => 'danger'];
                        break;
                    case Log::SUCCESS :
                        $rowColor = ['class' => 'success'];
                        break;
                    default:
                        $rowColor = ['class' => 'danger'];
                        break;
                }
                return $rowColor;
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'action',
                    'filter' => $action,
                    'filterInputOptions' => [
                        'id' => NULL,
                        'class' => 'form-control',
                        'prompt' => 'Все'
                    ],
                ],
                'ip',
                'user',
                [
                    'attribute' => 'description',
                    'format' => 'raw',  
                    'filter' => $description,
                    'filterInputOptions' => [
                        'id' => NULL,
                        'class' => 'form-control',
                        'prompt' => 'Все'
                    ],
                ],
                [
                    'attribute' => 'until',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $until = !empty(@unserialize($model->until)) ? @unserialize($model->until) : $model->until;
                        $result = '';
                        if (!empty($until) && is_array($until)) {
                            foreach ($until as $key => $value) {
                                if (!in_array($key, Yii::$app->params['hideParams'])) {
                                    if ($key == 'num') {
                                        $client = Client::find()->where(['num' => $value])->one();
                                        if ($client) {
                                            $result = $result . "<b>" . $key . ":" .
                                                Html::a(
                                                    $value,
                                                    Url::to(['client/view', 'id' => $client->id]),
                                                    [
                                                        'data-pjax' => 0
                                                    ]
                                                )
                                                . "<br>";
                                        } else {
                                            $result = $result . "<b>" . $key . ":</b> " . $value . "<br>";
                                        }
                                    } else {
                                        $result = $result . "<b>" . $key . ":</b> " . $value . "<br>";
                                    }
                                }
                            }
                        } else {
                            $result = $until;
                        }
                        return $result;
                    }
                ],
                [
                    'attribute' => 'after',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $after = !empty(@unserialize($model->after)) ? @unserialize($model->after) : $model->after;
                        $until = !empty(@unserialize($model->until)) ? @unserialize($model->until) : $model->until;
                        $result = '';
                        if (!empty($after) && is_array($after)) {
                            foreach ($after as $key => $value) {
                                if (!in_array($key, Yii::$app->params['hideParams'])) {
                                    if ($key == 'num') {
                                        $client = Client::find()->where(['num' => $value])->one();
                                        if ($client) {
                                            $result = $result . "<b>" . $key . ":" .
                                                Html::a(
                                                    $value,
                                                    Url::to(['client/view', 'id' => $client->id]),
                                                    [
                                                        'data-pjax' => 0
                                                    ]
                                                )
                                                . "<br>";
                                        }
                                    } else {
                                        if (!empty($until) && $until[$key] != $value) {
                                            $result = $result . "<font color='red'><b>" . $key . ":</b> " . $value . "<br></font>";
                                        } else {
                                            $result = $result . "<b>" . $key . ":</b> " . $value . "<br>";
                                        }
                                    }
                                }
                            }
                        } else {
                            $result = $after;
                        }
                        return $result;
                    }
                ],
                [
                'attribute' => 'create_at',
                'value' => 'create_at',
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'create_at',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-m-dd',
                            'todayHighlight' => true
                        ]
                ]),
                ],
            ],
            'layout' => "<div class='row'><div align='left' class='col-xs-6 form-inline'>" . $pageSize .  "</div><div align='right' class='col-xs-6'>{summary}</div></div><p>{items}<div align='center'>{pager}</div>"
        ]); ?>
    <?php Pjax::end(); ?>
</div>
