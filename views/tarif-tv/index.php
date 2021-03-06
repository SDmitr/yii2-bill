<?php

use yii\helpers\Html;
use yii\grid\GridView;
use nterms\pagesize\PageSize;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TarifTvSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Тарифы IPTv';
//$this->params['breadcrumbs'][] = $this->title;

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
<div class="tarif-tv-index">
<p>
    <?= Html::a('Create Tarif Tv', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
            'id',
            'name',
            'money',
        ],
        'layout' => "<div class='row'><div align='left' class='col-xs-6 form-inline'>" . $pageSize .  "</div><div align='right' class='col-xs-6'>{summary}</div></div><p>{items}<div align='center'>{pager}</div>"
    ]); ?>
<?php Pjax::end(); ?>
</div>
