<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Switches;

class SwitchController extends Controller {

    private $ip;


    public function actionUpdate() {

        $start = time();
        $devices = Yii::$app->params['managementNetwork'];

        $subnet = long2ip(ip2long($devices['subnet']) & ip2long($devices['mask']));
        $mask = $devices['mask'];
        $broadcast = long2ip(ip2long($subnet) | ~ip2long($mask)) ;

        $first_ip = ip2long($subnet) + 10;
        $last_ip = ip2long($broadcast) - 170;
        
        for ($address = $first_ip; $address <= ip2long('192.168.0.150'); $address++ ) {
            try {
                $ip = long2ip($address);
                $switch = Switches::findOne(array('ip' => $ip));

                if (empty($switch->id))
                {
                    $switch = new Switches();
                    $switch->ip = $ip;
                }

                $vendor = $switch->getVendor();
                $fdb = $switch->getFdb($vendor);
                $switch->name = $switch->getSwitchName();
                $switch->vendor = $vendor;
                $switch->interfaces = serialize($switch->getInterfaces());
                $switch->fdb = serialize($fdb);

                $switch->save();
                echo $ip . " производитель " . $switch->vendor . " название " . $switch->name . " кол-во портов " . $switch->interface_count . "\n";
            } catch (\Exception $e) {
                $switch->delete();
                echo $e->getMessage() . "\n";
            }
        }
        $stop = time();
        echo 'Execute time: ' . date('H:i:s', ($stop - $start)) . "\n";
    }
}
