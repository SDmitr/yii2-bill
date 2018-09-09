<?php

use app\models\Inet;
use app\models\TarifInet;
use yii\helpers\Html;
use yii\helpers\Url;

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

$this->title = $model->ip;

$deviceWidth = '75vw';

if (count($interfaces) <= 12)
{
    $deviceWidth = '60vw';
}
elseif (count($interfaces) >= 24)
{
    $deviceWidth = '77vw';
}

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

<div class="switches-view">
    <div class="row">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a(
                    '<span class="glyphicon glyphicon-chevron-left"></span> Назад',
                    ['inet/index'],
                    ['class' => 'btn btn-warning']
            ) ?>
        </div>
        <div  class="col-xs-9 col-sm-10">
            <h3 style="margin: 0 auto; text-align: center;"><?= Html::encode($this->title) ?></h3>
        </div>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'vendor'
        ],
    ]) ?>

    <div class="row device" style="width: <?= $deviceWidth ?>">
        <div class="row">
            <?php $i = 1 ?>
            <?php foreach ($interfaces as $id => $item): ?>
                <?php if ($i % 2 != 0 || count($interfaces) <= 12): ?>
                    <?php $status = ($item['status'] == 1) ? 'active' : '' ?>
                    <?php $adminStatus = ($item['admin_status'] == 2) ? 'shutdown' : '' ?>
                    <?php $vlanMode = ($item['vlan_mode'] == 2) ? 'trunk' : '' ?>
                    <?php $inet = Inet::findOne(['switch' => $model->id, 'interface' => $id]) ?>
                    <?php if($inet !== null): ?>
                        <?= Html::a(
                                '<div class="interface ' . $status . ' ' . $adminStatus . ' ' . $vlanMode . '">' . Html::encode($i) . '<i class="glyphicon glyphicon-pushpin" style="float: right;"></i></div>',
                                Url::to(['inet/view', 'id' => $inet->id]),
                                ['title' => $inet->client->name , 'data-pjax' => 0]
                        ) ?>
                    <?php else: ?>
                        <?= '<div class="interface ' . $status . ' ' . $adminStatus . ' ' . $vlanMode . '">' . Html::encode($i) . '</div>' ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php $i++ ?>
            <?php endforeach; ?>
            <?php if (isset($power) && $power == true): ?>
                <?php $icon = 'glyphicon-ok-sign'; ?>
                <?php $active = 'active'; ?>
            <?php else: ?>
                <?php $icon = 'glyphicon-alert'; ?>
                <?php $active = ''; ?>
            <?php endif; ?>
            <div class="power"><i class="glyphicon <?= $icon ?> <?= $active ?>"></i></div>
        </div>
        <?php if (count($interfaces) > 12): ?>
            <div class="row">
                <?php $i = 1 ?>
                <?php foreach ($interfaces as $id => $item): ?>
                    <?php if ($i % 2 == 0): ?>
                        <?php $status = ($item['status'] == 1) ? 'active' : '' ?>
                        <?php $adminStatus = ($item['admin_status'] == 2) ? 'shutdown' : '' ?>
                        <?php $vlanMode = ($item['vlan_mode'] == 2) ? 'trunk' : '' ?>
                        <?php $inet = Inet::findOne(['switch' => $model->id, 'interface' => $id]) ?>
                        <?php if($inet !== null): ?>
                            <?= Html::a(
                                    '<div class="interface ' . $status . ' ' . $adminStatus . ' ' . $vlanMode . '">' . Html::encode($i) . '<i class="glyphicon glyphicon-pushpin" style="float: right;"></i></div>',
                                    Url::to(['inet/view', 'id' => $inet->id]),
                                    ['title' => $inet->client->name , 'data-pjax' => 0]) ?>
                        <?php else: ?>
                            <?= '<div class="interface ' . $status . ' ' . $adminStatus . ' ' . $vlanMode . '">' . Html::encode($i) . '</div>' ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php $i++ ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php Pjax::begin(['timeout' => 60000, 'enablePushState' => false ]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
            [
                'attribute' => 'interface',
                'format' => 'raw',
                'value' => function ($model) {
                    $switch = $model->switches;
                    if($switch !== null) {
                        $interfaces = unserialize($switch->interfaces);
                        $interfaceName = isset($interfaces[$model->interface]['name']) ? $interfaces[$model->interface]['name'] : '';
                        return $interfaceName;
                    }
                    return false;
                },
            ],
            [
                'attribute' => 'client.num',
                'value' => 'client.num',
            ],
            [
                'attribute' => 'client.name',
                'value' => 'client.name',
            ],
            [
                'attribute' => 'client.street',
                'value' => 'client.street',
            ],
            [
                'attribute' => 'client.building',
                'value' => 'client.building',
            ],
            [
                'attribute' => 'client.room',
                'value' => 'client.room',
            ],
            'ip',
            'mac',
            [
                'attribute' => 'onu_mac',
                'format' => 'raw',
                'value' => function ($model) { return Html::a(Html::encode($model->onu_mac), Url::to(['pon/view', 'id' => $model->onu_mac ]), ['data-pjax' => 0]); },
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
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
    $this->registerJsFile('@web/js/get-arp.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
