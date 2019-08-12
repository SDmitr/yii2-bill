<?php

namespace app\components;

use yii\grid\ActionColumn;
use yii\helpers\Html;

class FilterActionColumn extends ActionColumn
{
    public function renderFilterCellContent()
    {
        return Html::resetButton('<span class="glyphicon glyphicon-refresh"></span>', ['id' => 'reset-filter', 'class' => 'btn btn-primary']);
    }
}