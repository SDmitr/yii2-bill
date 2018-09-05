<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Switches;
use app\models\Inet;

class InterfaceController extends Controller {

    public $switches = array();

    public function actionUpdate() {
        $inets = Inet::find()->all();
        $this->switches = Switches::find()->all();

        try {
            foreach ($inets as $inet)
            {
                if (!empty($inet->mac))
                {
                    $mac = strtolower(str_replace(':', '', $inet->mac));
                    $switch = $this->getSwitch($mac);

                    if (count($switch) == 2)
                    {
                        $inet->switch = $switch['ip'];
                        $inet->interface = $switch['interface'];
                        $inet->save();

                        echo 'Inet: ' . $inet->id . ' mac: ' . $inet->mac . ' switch: ' . $switch['ip'] . ' interface: ' . $switch['interface']. "\n";
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function getSwitch($mac)
    {
        $isFound = false;
        $result = array();
        foreach ($this->switches as $switch)
        {
            $macTable = unserialize($switch->fdb);

            foreach ($macTable as $key => $value)
            {
                if ($mac == $key && $this->getCount($macTable, $value) <= 2)
                {
                    $result['ip'] = $switch->ip;
                    $result['interface'] = $value;
                    $isFound = true;
                }
            }

            if ($isFound == true)
            {
                break;
            }
        }
        return $result;
    }

    public function getCount($macTable, $interface)
    {
        $result = 0;
        foreach ($macTable as $key => $value)
        {
            if ($value == $interface)
            {
                $result++;
            }
        }
        return $result;
    }
}

