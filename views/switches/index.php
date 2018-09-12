<?php

use yii\helpers\Html;
use yii\grid\GridView;
use nterms\pagesize\PageSize;
use yii\widgets\Pjax;
use app\models\Switches;
use app\models\Status;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SwitchesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Коммутаторы';
$this->params['breadcrumbs'][] = $this->title;

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
<div class="street-index">
<p>
    <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['stat/index'], ['class' => 'btn btn-warning']) ?>
</p>
<?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'rowOptions' => function($model) {
            if ($model->status == Switches::STATUS_DOWN) {
                return ['class' => 'danger'];

            }
        },
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
            'name',
            'vendor',

            [
                'attribute' => 'vendor',
                'value' => 'vendor',
                'filter' => Switches::find()->select(['vendor', 'vendor'])->indexBy('vendor')->column(),
                'filterInputOptions' => [
                    'vendor' => NULL,
                    'class' => 'form-control',
                    'prompt' => 'Все'
                ],
                'options' => ['style' => 'width:130px;'],
            ],

            'ip',
            [
                'attribute' => 'status',
                'value' => 'status.name',
                'filter' => Status::find()->select(['name', 'id'])->indexBy('id')->column(),
                'filterInputOptions' => [
                    'id' => NULL,
                    'class' => 'form-control',
                    'prompt' => 'Все'
                ],
                'options' => ['style' => 'width:130px;'],
            ],
        ],
        'layout' => "<div class='row'><div align='left' class='col-xs-6 form-inline'>" . $pageSize .  "</div><div align='right' class='col-xs-6'>{summary}</div></div><p>{items}<div align='center'>{pager}</div>"
    ]); ?>
<?php Pjax::end(); ?>
</div>
