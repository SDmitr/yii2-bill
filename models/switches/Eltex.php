<?php

namespace app\models\switches;

use Yii;
use app\models\Switches;

/**
 * Class Eltex
 * @package app\models\switches
 */
class Eltex extends Switches
{
    const OID_FDB = '1.3.6.1.2.1.17.7.1.2.2.1.2';

    const OID_INTERFACES = '1.0.8802.1.1.1.1.1.2.1.2';

    const VENDOR = 'Eltex';
}
