<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Switches;
use app\models\Inet;
use yii\helpers\Console;

/**
 * Class InterfaceController
 * @package app\commands
 */
class InterfaceController extends Controller
{
    const ONU_DISTR = array(
        'EPON0/4:22',
        'epon0/3:11'
    );
    /**
     * @var array
     */
    private $switches = array();

    /**
     *
     */
    public function actionUpdate()
    {
        $inets = Inet::find()->all();
        $this->switches = Switches::find()->all();

        $i = 0;
        Console::startProgress($i, count($inets));

        foreach ($inets as $inet) {
            try {
                if (!empty($inet->mac)) {
                    $mac = strtolower(str_replace(':', '', $inet->mac));
                    $switch = $this->getSwitch($mac);

                    if (count($switch) == 5) {
                        $inet->switch = $switch['ip'];
                        $inet->interface = $switch['interface_name'];
                        $inet->onu = $switch['onu'];
                        $inet->save();
                    }
                }
            } catch (\Exception $e) {
            }
            $i++;
            Console::updateProgress($i, count($inets));
        }
        Console::endProgress();
    }

    /**
     * @param $mac
     * @return array
     */
    private function getSwitch($mac)
    {
        $result = array();
        foreach ($this->switches as $switch) {
            $macTable = @unserialize($switch->fdb);
            $interfaceTable = @unserialize($switch->interfaces);

            foreach ($macTable as $key => $interface) {
                if ($mac == $key
                    && $this->getVlanMode($interfaceTable, $interface) == Switches::INTERFACE_ACCESS
                    && $switch->status_id == Switches::STATUS_UP
                    && !array_key_exists($interfaceTable[$interface]['onu'], Yii::$app->params['onuService'])
                ) {
                    $result['id'] = $switch->id;
                    $result['ip'] = $switch->ip;
                    $result['interface'] = $interface;
                    $result['interface_name'] = $interfaceTable[$interface]['name'];
                    $result['onu'] = $interfaceTable[$interface]['onu'];
                    return $result;
                }
            }
        }
        return $result;
    }

    /**
     * @param $macTable
     * @param $interface
     * @return int
     */
    private function getCount($macTable, $interface)
    {
        $result = 0;
        foreach ($macTable as $key => $value) {
            if ($value == $interface) {
                $result++;
            }
        }
        return $result;
    }

    /**
     * @param $interfaceTable
     * @param $interface
     * @return mixed
     */
    private function getVlanMode($interfaceTable, $interface)
    {
        return $interfaceTable[$interface]['vlan_mode'];
    }
}

