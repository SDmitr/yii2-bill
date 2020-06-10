<?php

namespace app\models\switches;

use Yii;
use app\models\Switches;

/**
 * Class Raisecom
 * @package app\models\switches
 */
class Raisecom extends Switches
{
    const OID_FDB = '1.3.6.1.2.1.17.7.1.2.2.1.2';

    public $vendor = 'Raisecom';
}
