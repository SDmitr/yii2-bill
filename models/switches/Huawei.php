<?php

namespace app\models\switches;

use Yii;
use app\models\Switches;

/**
 * Class Huawei
 * @package app\models\switches
 */
class Huawei extends Switches
{
    const OID_FDB = '1.3.6.1.2.1.17.4.3.1.2';

    public $vendor = 'Huawei';

    public function setFdb()
    {
        $this->fdb = @serialize(array());
    }
}
