<?php
use yii\grid\GridView;
use nterms\pagesize\PageSize;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
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
<div class="client-index">
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
    <?= GridView::widget([
        'id' => 'grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterSelector' => 'select[name="per-page"]',
        'columns' => [
            [
                'class' => 'app\components\FilterActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-eye-open"></span>',
                            Url::to(['view', 'id' => $model->id]),
                            [
                                'data-pjax' => 0
                            ]);
                    }
                ]
            ],
            'num',
            'name',
            'street',
            'building',
            'room',
            [
                'attribute' => 'phone_1',
                'format' => 'raw',
                'value' => function ($model) {
                    return !empty($model->phone_1) ? Html::a($model->phone_1, 'tel:' . $model->phone_1) : '';
                },
            ],
            [
                'attribute' => 'phone_2',
                'format' => 'raw',
                'value' => function ($model) {
                    return !empty($model->phone_2) ? Html::a($model->phone_2, 'tel:' . $model->phone_2) : '';
                },
            ],
            'email:email',
        ],
	'layout' => "<div class='row'><div align='left' class='col-xs-6 form-inline'>" . $pageSize .  "</div><div align='right' class='col-xs-6'>{summary}</div></div><p>{items}<div align='center'>{pager}</div>"
    ]); ?>
<?php Pjax::end(); ?>
</div>

<?php
    $this->registerJsFile('@web/js/reset-filter.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
