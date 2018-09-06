<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use \SNMP;
use app\models\Switches;

class SwitchController extends Controller {

    private $ip;
    private $fdbOid = array(
        'Nexthop'       => '1.3.6.1.2.1.17.7.1.2.2.1.2',
        'Foxgate'       => '1.3.6.1.2.1.17.7.1.2.2.1.2',
        'Huawei'        => '1.3.6.1.2.1.17.4.3.1.2',
        'Foxgate S6008' => '1.3.6.1.2.1.17.7.1.2.3.1.2',
        'Cisco'         => '1.3.6.1.2.1.17.7.1.2.3.1.2',
        'ROS'           => '1.3.6.1.2.1.17.7.1.2.2.1.2',
        'Edge-core'     => '1.3.6.1.2.1.17.7.1.2.2.1.2',
        'BDCOM'         => '1.3.6.1.4.1.3320.152.1.1.1',
        'Unknown'       => '1.3.6.1.2.1.17.7.1.2.2.1.2',


    );

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
                $this->ip = long2ip($address);
                $switch = Switches::findOne(array('ip' => $this->ip));

                if (empty($switch->id))
                {
                    $switch = new Switches();
                    $switch->ip = $this->ip;
                }

                $vendor = $this->getVendor();
                $fdb = $this->getFdb($vendor);
                $switch->name = $this->getSwitchName();
                $switch->vendor = $vendor;
                $switch->interface_count = $this->getInterfaces();
                $switch->fdb = serialize($fdb);

                $switch->save();
                echo $this->ip . " производитель " . $switch->vendor . " название " . $switch->name . " кол-во портов " . $switch->interface_count . "\n";
            } catch (\Exception $e) {
                $switch->delete();
                echo $e->getMessage() . "\n";
            }
        }
        $stop = time();
        echo 'Execute time: ' . date('H:i:s', ($stop - $start)) . "\n";
    }
    
    private function getFdb($vendor)
    {
        $result = array();
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 60000000, 1);
        $session->oid_increasing_check = false;
//        if ($session->getError()) throw new \Exception ($session->getError());

        if (!empty($this->fdbOid[$vendor]))
        {
            $oid = $this->fdbOid[$vendor];
        } else {
            $oid = $this->fdbOid['Unknown'];
        }

        $fdb = @$session->walk($oid);

        if (is_array($fdb)) {
            foreach ($fdb as $oid => $interface) {
                $components = explode('.', $oid);
                $macArray = array_slice($components, -6, 6);

                $mac = '';
                foreach ($macArray as $value) {

                    $octet = dechex($value);
                    if (strlen($octet) == 1) {
                        $octet = '0' . $octet;
                    }

                    $mac .= strtolower($octet);
                }

                $interface = preg_replace('/\D/', '', $interface);

                $result[$mac] = $this->getInterfaceName($interface);
            }
        }
        $session->close();
        return $result;
        
    }

    private function getInterfaceName($id)
    {
        $name = 'unknown';
        if ($id > 0) {
            $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 1000000, 1);
            $session->oid_increasing_check = false;
            $interface = @$session->get('1.3.6.1.2.1.2.2.1.2.' . $id);
            if ($session->getError()) throw new \Exception ($session->getError());

            preg_match('~\w+\:\s\"(.+)\"~', $interface, $interfaceName);

            if (!empty($interfaceName[1])) {
                $name = $interfaceName[1];
            }
            $session->close();
        }
        return $name;
    }

    private function getInterfaces()
    {
        $result = 0;
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 1000000, 1);
        $session->oid_increasing_check = false;
        $interfaces = @$session->walk('1.3.6.1.2.1.2.2.1.3');
        if ($session->getError()) throw new \Exception ($session->getError());

        if (!empty($interfaces)) {
            foreach ($interfaces as $oid => $interface) {
                $interfaceType = preg_replace('/\D/', '', $interface);
                if ($interfaceType == 6) {
                    $result++;
                }
            }
        }
        $session->close();
        return $result;
    }

    private function getSwitchName()
    {
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $string = @$session->get("1.3.6.1.2.1.1.5.0");
        if ($session->getError()) throw new \Exception ($session->getError());

        preg_match('~\w+\:\s\"(\w+)\"~', $string, $switchName);

        if (empty($switchName[1])) {
            $name = $this->ip;
        } else {
            $name = $switchName[1];
        }
        $session->close();
        return $name;
    }

    private function getVendor()
    {
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $system = @$session->get("1.3.6.1.2.1.1.1.0");
        if ($session->getError()) throw new \Exception ($session->getError());

        if (strpos($system, 'NH-') || strpos($system, 'Hex-STRING') === 0) {
            return 'Nexthop';
        } else if (strpos($system, 'ES')) {
            return 'Edge-core';
        } else if (strpos($system, 'Huawei')) {
            return 'Huawei';
        } else if (strpos($system, 'S62')) {
            return 'Foxgate';
        } else if (strpos($system, 'Cisco')) {
            return 'Cisco';
        } else if (strpos($system, 'ROS')) {
            return 'ROS';
        } else if (strpos($system, 'NBA')) {
            return 'NBA';
        } else if (strpos($system, 'Layer 2 Management Switch')) {
            return 'Foxgate S6008';
        } else if (strpos($system, 'BDCOM')) {
            return 'BDCOM';
        } else {
            return 'Unknown';
        }

        return 'Unknown';
    }
}

