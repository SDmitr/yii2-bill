<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Switches;
use yii\helpers\Console;

/**
 * Class SwitchController
 * @package app\commands
 */
class SwitchController extends Controller
{

    /**
     *
     */
    public function actionUpdate()
    {

        $start = time();
        $devices = Yii::$app->params['managementNetwork'];

        $subnet = long2ip(ip2long($devices['subnet']) & ip2long($devices['mask']));
        $mask = $devices['mask'];
        $broadcast = long2ip(ip2long($subnet) | ~ip2long($mask));

        $first_ip = ip2long($subnet) + 6;
        $last_ip = ip2long('192.168.0.150');

        $i = 0;
        $switchCount = $last_ip - $first_ip;
        Console::startProgress($i, $switchCount);

        for ($address = $first_ip; $address <= $last_ip; $address++) {
            try {
                $ip = long2ip($address);
                $model = Switches::getSwitchEntity($ip);
                if ($model) {
                    $switch = $model->findByIp($ip);
                    if (is_null($switch)) {
                        $switch = $model;
                    }
                    $switch->ip = $ip;
                    $switch->aton = ip2long($ip);
                    $switch->status_id = Switches::STATUS_UP;
                    $switch->setVendor();
                    $switch->setSwitchName();
                    $switch->setInterfaces();
                    $switch->setFdb();
                    $switch->save();
                }
            } catch (\Exception $e) {
                if (isset($switch) && $switch->id) {
                    $switch->status_id = Switches::STATUS_DOWN;
                    $switch->save();
                }
            }
            unset($switch);
            $i++;
            Console::updateProgress($i, $switchCount);
        }
        Console::endProgress();
        $stop = time();
        echo 'Execute time: ' . date('H:i:s', ($stop - $start)) . "\n";
    }
}

