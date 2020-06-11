<?php

namespace app\models\switches;

use SNMP;
use Yii;
use app\models\Switches;

/**
 * Class Bdcom
 * @package app\models\switches
 */
class Bdcom extends Switches
{
    const OID_FDB = '1.3.6.1.4.1.3320.152.1.1.1';

    const VENDOR = 'BDCOM';

    /**
     * @throws \Exception
     */
    public function setInterfaces()
    {
        $result = empty($this->interfaces) ? array() : @unserialize($this->interfaces);

        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 1000000, 1);
        $session->oid_increasing_check = false;
        $interfaces = @$session->walk(static::OID_INTERFACES);
//        if ($session->getError()) throw new \Exception ($session->getError());

        if (!empty($interfaces)) {
            $result = array();
            foreach ($interfaces as $oid => $interface) {
                $components = explode('.', $oid);
                $id = array_pop($components);

                $interfaceType = @$session->get( static::OID_INTERFACE_TYPE . $id);
                $interfaceType = preg_replace('/.+\:/', '', $interfaceType);
                if ($interfaceType == 6 || $interfaceType == 1) {
                    $name = @$session->get(static::OID_INTERFACE_NAME . $id);
                    $onu = @$session->get('1.3.6.1.4.1.3320.101.10.1.1.3.' . $id);
                    $macOnu = strtolower(str_replace('Hex-STRING: ', '', $onu));
                    $macOnu = preg_replace('/\s+/', ':', trim($macOnu));
                    preg_match('~\w+\:\s\"(.+)\"~', $name, $interfaceName);
                    $result[$id]['name'] = !empty($interfaceName[1]) ? $interfaceName[1] : $id;
                    $result[$id]['type'] = $interfaceType;
                    $result[$id]['onu'] = $macOnu;
                    $result[$id]['vlan_mode'] = $this->setVlanMode($id);
                }
            }
        }
        @$session->close();

        $this->interfaces = @serialize($result);
    }
}
