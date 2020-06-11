<?php
use app\models\TarifInet;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\bootstrap\Modal;


/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Modal::begin([
    'id' => 'arp-response',
    'header' => '<h4>Результат ARP-запроса</h4>',
]) ?>
    <div class="row">
        <div class="col-xs-6">IP-адрес:</div>
        <div class="col-xs-6"><span id="ip"></span></div>
    </div>
    <div class="row">
        <div class="col-xs-6">Состояние:</div>
        <div class="col-xs-6"><span id="icon"></span> <span id="status"></span></div>
    </div>
<?php Modal::end(); ?>



<div class="client-view">
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['index'], ['class' => 'btn btn-warning']) ?>
        </div>
        <div  class="col-xs-9 col-sm-10">
            <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    <p>
    <div class="row">
        <div class="col-xs-6 col-sm-8">
            <?= Yii::$app->user->can('updateClient') ? Html::a('<span class="glyphicon glyphicon-pencil"></span> Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : '' ?>
        </div>
        <div class="col-xs-6 col-sm-4" align="right">
            <?= Yii::$app->user->can('deleteClient') ? Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить данного пользователя?',
                    'method' => 'post',
                ],
            ]) : '' ?>
        </div>
    </div>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'num',
            'name',
            [
                'attribute' => 'street',
                'value' => $model->street . ', ' . $model->building . ', ' .$model->room,
                'label' => 'Адрес'
            ],
            [
                'attribute' => 'phone_1',
                'format'=>'raw',
                'value'=> !empty($model->phone_1) ? Html::a($model->phone_1, 'tel:' . $model->phone_1) : '',
            ],
            [
                'attribute' => 'phone_2',
                'format'=>'raw',
                'value'=> !empty($model->phone_2) ? Html::a($model->phone_2, 'tel:' . $model->phone_2) : '',
            ],
            'email:email',
        ],
    ]) ?>

    <p>  
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Добавить подключение Интернет', ['inet/create', 'id' => $model->id, 'num' => $model->num ], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $model->getInets()]),
            'columns' => [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'controller' => 'inet',
                ],                
                [
                    'label' => 'ARP',
                    'format' => 'raw',
                    'value' => function ($model) { return Html::a('<span class="glyphicon glyphicon-refresh"></span>', ['inet/arp', 'id' => $model->id], [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#arp-response',
                                    ]);
                    },
                    'visible' =>  Yii::$app->user->can('arpInet'),
                ],
                'ip',
                'mac',
                'comment',
                [
                    'attribute' => 'switch',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $switch = $model->switches;
                        if($switch !== null) {
                            return Html::a(
                                    Html::encode($switch->ip),
                                    Url::to(['switches/view', 'id' => $switch->id]),
                                    ['title' => $switch->name , 'data-pjax' => 0, 'class' => 'btn btn-default btn-xs btn-block']
                            );
                        }
                        return false;
                    },
                ],
                [
                    'attribute' => 'interface',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if($model->interface !== null) {
                            return $model->interface;
                        }
                        return false;
                    },
                ],
                [
                    'attribute' => 'onu_mac',
                    'format' => 'raw',
                    'value' => function ($model) { return Html::a(Html::encode($model->onu_mac), Url::to(['pon/view', 'id' => $model->onu_mac ]), ['data-pjax' => 0]); },
                ],
                [
                    'attribute' => 'tarif_id',
                    'filter' => TarifInet::find()->select(['name', 'id'])->indexBy('id')->column(),
                    'value' => 'tarif.name'
                ],
                Yii::$app->user->can('statusInet') ?            
                [
                    'class' => '\dixonstarter\togglecolumn\ToggleColumn',
                    'controller' => 'inet',
                    'attribute' => 'status_id',
                    'options' => ['style' => 'width:130px;'],
                    'linkTemplateOn' => '<a style="width:80px;" class="toggle-column btn btn-primary btn-xs btn-block" data-pjax="0" href="{url}"><i  class="glyphicon glyphicon-ok"></i> {label}</a>',
                    'linkTemplateOff' => '<a style="width:80px;" class="toggle-column btn btn-danger btn-xs btn-block" data-pjax="0" href="{url}"><i  class="glyphicon glyphicon-remove"></i> {label}</a>',
                ] :
                [
                    'attribute' => 'status_id',
                    'value' => 'status.name',
                    'options' => ['style' => 'width:130px;'],
                ],
    //            'date_on',
    //            'date_off',
                'date_create',
            ],
        ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php
    $this->registerJsFile('@web/js/get-arp.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
