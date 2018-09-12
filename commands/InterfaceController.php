<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Switches;
use app\models\Inet;

class InterfaceController extends Controller {

    private $switches = array();

    public function actionUpdate() {
        $inets = Inet::find()->all();
        $this->switches = Switches::find()->all();

        foreach ($inets as $inet)
        {
            try {
                if (!empty($inet->mac))
                {
                    $mac = strtolower(str_replace(':', '', $inet->mac));
                    $switch = $this->getSwitch($mac);

                    if (count($switch) == 2)
                    {
                        $inet->switch = $switch['id'];
                        $inet->interface = $switch['interface'];

                        if ($inet->save())
                        {
                            echo 'Inet: ' . $inet->id . ' mac: ' . $inet->mac . ' switch: ' . $switch['id'] . ' interface: ' . $switch['interface'] . "\n";
                        } else {

                            $error = array_values($inet->getFirstErrors());
                            throw new \Exception ($error[0]);
                        }
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }
    }

    private function getSwitch($mac)
    {
        $isFound = false;
        $result = array();
        foreach ($this->switches as $switch)
        {
            $macTable = unserialize($switch->fdb);
            $interfaceTable = unserialize($switch->interfaces);
            
            foreach ($macTable as $key => $interface)
            {
                if ($mac == $key && $this->getVlanMode($interfaceTable, $interface) == Switches::INTERFACE_ACCESS && $switch->status_id == Switches::STATUS_UP)
                {
                    $result['id'] = $switch->id;
                    $result['interface'] = $interface;
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

    private function getCount($macTable, $interface)
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
    
    private function getVlanMode($interfaceTable, $interface)
    {
        return $interfaceTable[$interface]['vlan_mode'];
    }
}

