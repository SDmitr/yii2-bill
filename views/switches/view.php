<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\models\Inet;

$this->title = $model->ip;
?>

<div class="switches-view">
    <div class="row">
        <div class="col-xs-3 col-sm-1">
            <?= Html::a('<span class="glyphicon glyphicon-chevron-left"></span> Назад', ['inet/index'], ['class' => 'btn btn-warning']) ?>
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

    <div class="row device">
        <div class="row">
            <?php $i = 1 ?>
            <?php foreach ($interfacesStatus as $id => $item): ?>
                <?php if ($i % 2 != 0 || count($interfacesStatus) <= 12): ?>
                    <?php $status = ($item == 1) ? 'active' : '' ?>
                    <?php $inet = Inet::findOne(['switch' => $model->id, 'interface' => $id]) ?>
                    <div class="interface <?= $status ?>">
                        <?php if($inet !== null): ?>
                            <?= Html::a(Html::encode($i), Url::to(['inet/view', 'id' => $inet->id]), ['title' => $inet->client->name ], ['data-pjax' => 0]) ?>
                        <?php else: ?>
                            <?= $i ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php $i++ ?>
            <?php endforeach; ?>
        </div>

        <?php if (count($interfacesStatus) > 12): ?>
            <div class="row">
                <?php $i = 1 ?>
                <?php foreach ($interfacesStatus as $id => $item): ?>
                    <?php if ($i % 2 == 0): ?>
                        <?php $status = ($item == 1) ? 'active' : '' ?>
                        <?php $inet = Inet::findOne(['switch' => $model->id, 'interface' => $id]) ?>
                        <div class="interface <?= $status ?>">
                            <?php if($inet !== null): ?>
                                <?= Html::a(Html::encode($i), Url::to(['inet/view', 'id' => $inet->id]), ['title' => $inet->client->name ], ['data-pjax' => 0]) ?>
                            <?php else: ?>
                                <?= $i ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php $i++ ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
