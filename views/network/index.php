<?php

use yii\helpers\Html;
use yii\grid\GridView;
use nterms\pagesize\PageSize;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NetworkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Локации';
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
<div class="network-index">
<p>
    <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['stat/index'], ['class' => 'btn btn-warning']) ?>
    <?= Yii::$app->user->can('createNetwork') ? Html::a('<span class="glyphicon glyphicon-plus"></span> Добавить локацию', ['create'], ['class' => 'btn btn-success']) : '' ?>
</p>
<?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}', 
            ],
            'name',
            'first_ip',
            'last_ip',
            'subnet',
            'mask',
            'gateway',
            'dns1',
            'dns2' 
        ],
        'layout' => "<div class='row'><div align='left' class='col-xs-6 form-inline'>" . $pageSize .  "</div><div align='right' class='col-xs-6'>{summary}</div></div><p>{items}<div align='center'>{pager}</div>"
    ]); ?>
<?php Pjax::end();?>
    
</div>
