<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use app\models\TarifTv;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Inet */

$this->title = $model->client->name;
$this->params['breadcrumbs'][] = ['label' => 'Интернет', 'url' => ['index']];
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

<div class="inet-view">
    <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
    <p>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-6">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['index'], ['class' => 'btn btn-warning']) ?>
        </div>
        <div class="col-xs-6" align="right">
            <?= Html::a('<span class="glyphicon glyphicon-user"></span> Пользователь', ['client/view', 'id' => $model->client->id], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <p>
    <div class="row">
        <div class="col-xs-6">
            <?= Yii::$app->user->can('updateClient') ? Html::a('<span class="glyphicon glyphicon-pencil"></span> Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : '' ?>
            <?= Yii::$app->user->can('arpInet') ? Html::a('<span class="glyphicon glyphicon-refresh"></span> ARP-запрос', ['arp', 'id' => $model->id], [ 'data-toggle' => 'modal', 'data-target' => '#arp-response', 'class' => 'btn btn-info' ]) : '' ?>
        </div>
        <div class="col-xs-6" align="right">
            <?= Yii::$app->user->can('deleteClient') ? Html::a('<span class="glyphicon glyphicon-trash"></span> Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить данное подключение?',
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
            [
                'attribute' => 'comment',
                'visible' => !empty($model->comment),
            ], 
            [
                'attribute' => 'client.name',
                'value' => $model->client->name,
            ],
            [
                'attribute' => 'client.street',
                'value' => $model->client->street . ', ' . $model->client->building . ', ' .$model->client->room,
                'label' => 'Адрес'
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
                'value' => $model->tarif->name,
            ],
            [
                'attribute' => 'status_id',
                'value' => $model->status->name,
            ],
            'date_create',
        ],
    ]) ?>

    
       
    
    <p>  
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Добавить подключение Tv', ['tv/create', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
        <?= GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => $model->getTv()]),
            'columns' => [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'controller' => 'tv',
                ],
                [
                    'attribute' => 'tarif_id',
                    'filter' => TarifTv::find()->select(['name', 'id'])->indexBy('id')->column(),
                    'value' => 'tarif.name'
                ],
                [
                    'attribute' => 'status_id',
                    'value' => 'status.name',
                    'options' => ['style' => 'width:130px;'],
                ],
                'date_create',
            ],
        ]); ?>
    <?php Pjax::end(); ?>   
</div>

<?php
    $this->registerJsFile('@web/js/get-arp.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
