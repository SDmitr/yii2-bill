<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Status;
use app\models\TarifTv;
use nterms\pagesize\PageSize;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\TvSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tvs';
$this->params['breadcrumbs'][] = $this->title;


$this->title = 'Интернет';
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
                                'vertical-align' => 'middle',
                            ]
                        ]
                    ]);
?>
<div class="tv-index">
<?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'rowOptions' => function($model) {
                if ($model->status_id == 2) {
                    return ['class' => 'danger'];
                    
                }
            },
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',   
            ],
            [
                'attribute' => 'num',
                'label' => 'Договор',
                'value' => 'inet.num',
            ],
            [
                'attribute' => 'name',
                'label' => 'ФИО',
                'value' => 'inet.client.name',
            ],
            [
                'attribute' => 'street',
                'label' => 'Улица',
                'value' => 'inet.client.street',
            ],
            [
                'attribute' => 'building',
                'label' => 'Дом',
                'value' => 'inet.client.building',
            ],
            [
                'attribute' => 'room',
                'label' => 'Квартира',
                'value' => 'inet.client.room',
            ],
            [
                'attribute' => 'tarif_id',
                'filter' => TarifTv::find()->select(['name', 'id'])->indexBy('id')->column(),
                'filterInputOptions' => [
                    'id' => NULL,
                    'class' => 'form-control',
                    'prompt' => 'Все'
                ],
                'value' => 'tarif.name',
                'contentOptions' => [
                    'class' => 'text-nowrap',
                    'style' => 'width: 120px;'
                ]
            ],
            [
                'attribute' => 'status_id',
                'filter' => Status::find()->select(['name', 'id'])->indexBy('id')->column(),
                'filterInputOptions' => [
                    'id' => NULL,
                    'class' => 'form-control',
                    'prompt' => 'Все'
                ],
                'value' => 'status.name',
                'contentOptions' => [
                    'class' => 'text-nowrap',
                    'style' => 'width: 120px;'
                ]
            ],
            //'date_on',
            //'date_off',
            'date_create',
        ],
                    'layout' => "<div class='row'><div align='left' class='col-xs-6 form-inline'>" . $pageSize .  "</div><div align='right' class='col-xs-6'>{summary}</div></div><p>{items}<div align='center'>{pager}</div>"
    ]); ?>
<?php Pjax::end(); ?>
</div>
