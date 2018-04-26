<?php
use app\models\Client;
use app\models\TarifInet;
use app\models\Status;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use nterms\pagesize\PageSize;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

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
<div class="row" style="margin-bottom: 10px;">
    <div class="col-xs-12">
        <div id="sms" class="btn btn-info" style="display: none;">
            <span class="glyphicon glyphicon-save" data-pjax="0" ></span> Файл SMS-рассылки
        </div>
    </div>
</div>
<div class="inet-index">
<?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false]);?>
    <?= GridView::widget([
        'id' => 'grid',
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
                'class' => 'yii\grid\CheckboxColumn',
             ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',   
            ],
            'num',
            [
                'attribute' => 'client',
                'label' => 'ФИО',
                'value' => 'client.name',
            ],
            [
                'attribute' => 'street',
                'label' => 'Улица',
                'value' => 'client.street',
            ],
            [
                'attribute' => 'building',
                'label' => 'Дом',
                'value' => 'client.building',
            ],
            [
                'attribute' => 'room',
                'label' => 'Квартира',
                'value' => 'client.room',
            ],
            'ip',
            'mac',
            'switch',
            'interface',
            [
                'attribute' => 'onu_mac',
                'format' => 'raw',
                'value' => function ($model) { return Html::a(Html::encode($model->onu_mac), Url::to(['pon/view', 'id' => $model->onu_mac ]), ['data-pjax' => 0]); },
            ],
            [
                'attribute' => 'tarif_id',
                'filter' => TarifInet::find()->select(['name', 'id'])->indexBy('id')->column(),
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
            Yii::$app->user->can('statusInet') ?
            [
                'class' => '\dixonstarter\togglecolumn\ToggleColumn',
                'attribute' => 'status_id',
                'options'=>['style'=>'width:130px;'],
                'linkTemplateOn'=>'<a style="width:90px;" class="toggle-column btn btn-primary btn-xs btn-block" href="{url}"><i  class="glyphicon glyphicon-ok"></i> {label}</a>',
                'linkTemplateOff'=>'<a style="width:90px;" class="toggle-column btn btn-danger btn-xs btn-block" href="{url}"><i  class="glyphicon glyphicon-remove"></i> {label}</a>',
                'filterInputOptions' => [
                    'id' => NULL,
                    'class' => 'form-control',
                    'prompt' => 'Все'
                ],
            ] : 
            [
                'attribute' => 'status_id',
                'value' => 'status.name',
                'filter' => Status::find()->select(['name', 'id'])->indexBy('id')->column(),
                'filterInputOptions' => [
                    'id' => NULL,
                    'class' => 'form-control',
                    'prompt' => 'Все'
                ],
                'options' => ['style' => 'width:130px;'],
            ],
            [
                'attribute' => 'date_create',
                'value' => 'date_create',
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_create',
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

<?php
    $this->registerJsFile('@web/js/get-phone.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>