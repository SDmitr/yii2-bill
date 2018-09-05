<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\Telnet;
use app\models\Pon;
use \SNMP;

class PonController extends Controller {
    public function actionCreate($host) {
        $date = Yii::$app->getFormatter()->asDatetime(time());
        $telnet = Yii::$app->params['telnetSettings'];
        
        $devices = Yii::$app->params['OLT'];
        
        foreach ($devices as $device) {
            if ($device['address'] == $host) break;
        }

        $olt = new Telnet($device['address'], $device['login'], $device['password'], $telnet['port'], $telnet['length']);

        for ($i=1; $i<=4; $i++){
            
            $olt->connect();
            $onuList = $olt->getOnu($i);
            $active = $olt->getStatus($i, 'active');
            $inactive = $olt->getStatus($i, 'inactive');
            foreach($onuList as $string){
                $iface = 'EPON0/' . $i;
                $string = trim($string);
                if (preg_match('/bind-onu/', $string)){
                    $bind = preg_split("/\s+/", $string);
                    $mac = $olt->getMac($bind[3]);
                    $onu = 'epon0/' . $i . ':' . $bind[4];
                    $onuinfo = $olt->getDiagOnu($onu);
                    
                    $pon = new Pon();
                    $pon->host = (string) $device['name'];
                    $pon->interface = (string) $onu;
                    $pon->mac = (string) $mac;
                    $pon->olt_power = (float) $olt->getPowerOlt($onu);
                    $pon->onu_power = (float) $onuinfo['power'];
                    $pon->transmitted_power = (float) $onuinfo['transmitted_power'];
                    $pon->temperature_onu = (float) $onuinfo['temperature'];
                    $pon->date = $date;

                    foreach ($active as $string) {
                        if (stripos($string, $onu . ' ') !== FALSE){
                            $distance = preg_split("/\s+/", $string);
                            $pon->distance = (int) $distance[6]/2;
                            $pon->reason = (string) 'включена';
                            break;
                        }
                    }

                    foreach ($inactive as $string) {
                        if (stripos($string, $onu . ' ') !== FALSE){
                            $reason = preg_split("/\s+/", $string);
                            $pon->distance = 0;
                          
                            if ($reason[5] == 'wire' ) {
                                $pon->reason = (string) 'кабель';
                            } elseif ($reason[5] == 'power') {
                                $pon->reason = (string) 'питание';
                            } else {
                                $pon->reason = (string) 'неизвестно';
                            }
                            break;
                        }
                    }
                    $pon->save();
                }       
            }
            $olt->close();
        }
    }
    
    public function actionTest() {
        
        $devices = Yii::$app->params['managementNetwork'];

        
        $subnet = long2ip(ip2long($devices['subnet']) & ip2long($devices['mask']));
        $mask = $devices['mask'];
        $broadcast = long2ip(ip2long($subnet) | ~ip2long($mask)) ;

        $first_ip = ip2long($subnet) + 14;
        $last_ip = ip2long($broadcast) - 170;
        
        for ($ip = $first_ip; $ip <= ip2long('192.168.0.100'); $ip++ ) {
            try {
                $session = new SNMP(SNMP::VERSION_2c, long2ip($ip), Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
                $sysdescr = @$session->get("1.3.6.1.2.1.1.1.0");
                

                $hostname = @$session->get("1.3.6.1.2.1.1.5.0");
                $ports = @$session->get("1.3.6.1.2.1.2.1.0");
                if ($session->getError())  throw new \Exception ($session->getError());
                echo long2ip($ip) . " производитель " . self::getModel($sysdescr) . " название " . $hostname . " кол-во портов " . $ports . "<br>";

            } catch (\Exception $e) {
                echo $e->getMessage() . "<br>";
            }
        }
    }
    
    public function getModel($sysdescr) {
        if (strpos($sysdescr, 'NH-') || strpos($sysdescr, 'Hex-STRING') === 0) return 'Nexthop';
        if (strpos($sysdescr, 'ES')) return 'Edge-core';
        if (strpos($sysdescr, 'Huawei')) return 'Huawei';
        if (strpos($sysdescr, 'S62')) return 'Foxgate';
        if (strpos($sysdescr, 'Cisco')) return 'Cisco';
        if (strpos($sysdescr, 'ROS')) return 'ROS';
        
        return 'Unknown';
    }
    
}

