<?php

namespace app\models;

use Yii;
use \SNMP;

/**
 * This is the model class for table "switches".
 *
 * @property integer $id
 * @property string $name
 * @property string $vendor
 * @property string $oid
 * @property string $ip
 * @property string $interfaces
 * @property string $fdb
 * @property integer $status
 *
 * @property Switches[] $switches
 */
class Switches extends \yii\db\ActiveRecord
{
    const STATUS_UP = 1;
    const STATUS_DOWN = 2;
    
    const INTERFACE_ACCESS = 1;
    const INTERFACE_TRUNK = 2;

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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'switches';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'vendor' => 'Производитель',
            'oid' => 'OID',
            'ip' => 'IP-адрес',
            'interfaces' => 'Интерфейсы',
            'fdb' => 'MAC-таблица',
            'status' => 'Статус'
        ];
    }

    public function getSwitchName()
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

    public function getInterfacesStatus()
    {
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $interfaces = unserialize($this->interfaces);

        if (!empty($interfaces))
        {
            foreach ($interfaces as $id => $item)
            {
                if (isset($item['type']) && $item['type'] == 6) {
                    try {
                        $status = @$session->get('1.3.6.1.2.1.2.2.1.8.' . $id);
                        if ($session->getError()) throw new \Exception ($session->getError());
                        $interfaceStatus = preg_replace('/\D/', '', $status);
                        $result[$id]['status'] = $interfaceStatus;
                        $result[$id]['vlan_mode'] = $item['vlan_mode'];
                        $result[$id]['admin_status'] = $item['status'];
                    } catch (\Exception $e) {
                        foreach ($interfaces as $id => $item)
                        {
                            $result[$id]['status'] = self::STATUS_DOWN;
                            $result[$id]['vlan_mode'] = self::INTERFACE_ACCESS;
                            $result[$id]['admin_status'] = self::STATUS_UP;
                        }
                        break;
                    }
                }
            }
        }

        $session->close();
        return $result;
    }

    public function getInterfaces()
    {
        $result = array();
        if (!empty($this->interfaces))
        {
            $result = unserialize($this->interfaces);
        }

        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 1000000, 1);
        $session->oid_increasing_check = false;
        $interfaces = @$session->walk('1.3.6.1.2.1.2.2.1.3');

        if ($session->getError()) throw new \Exception ($session->getError());

        if (!empty($interfaces))
        {
            $result = array();
            foreach ($interfaces as $oid => $interface)
            {
                $components = explode('.', $oid);
                $id = array_pop($components);
                $interfaceType = preg_replace('/\D/', '', $interface);
                if ($interfaceType == 6 || $interfaceType == 1)
                {
                    $name = @$session->get('1.3.6.1.2.1.2.2.1.2.' . $id);
                    preg_match('~\w+\:\s\"(.+)\"~', $name, $interfaceName);
                    $result[$id]['name'] = !empty($interfaceName[1]) ? $interfaceName[1] : $id;
                    $result[$id]['type'] = $interfaceType;
                    $result[$id]['status'] = $this->getStatus($id);
                    $result[$id]['vlan_mode'] = $this->getVlanMode($id);
                }
            }
        }

        $session->close();
        return $result;
    }
    
    public function getStatus($id)
    {
        $result = '';
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $status = @$session->get('1.3.6.1.2.1.2.2.1.7.' . $id);
        $status = preg_replace('/\D/', '', $status);
        
        return $status;
    }
    
    public function getVlanMode($id)
    {
        $result = self::INTERFACE_TRUNK;
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $vlan = @$session->get('1.3.6.1.2.1.17.7.1.4.5.1.1.' . $id);
        preg_match('~\w+\:\s(\w+)~', $vlan, $vlanMode);
        if (isset($vlanMode[1]) && $vlanMode[1] > 1 && $vlanMode[1] != 1000) {
            $result = self::INTERFACE_ACCESS;
        }
        
        return $result;
    }

    public function getVendor()
    {
        if (empty($this->vendor))
        {
            $result = 'Unknown';
        } else {
            $result = $this->vendor;
        }
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $system = @$session->get("1.3.6.1.2.1.1.1.0");
        if ($session->getError()) throw new \Exception ($session->getError());

        if (strpos($system, 'NH-') || strpos($system, 'Hex-STRING') === 0) {
            $result = 'Nexthop';
        } else if (strpos($system, 'ES')) {
            $result = 'Edge-core';
        } else if (strpos($system, 'Huawei')) {
            $result = 'Huawei';
        } else if (strpos($system, 'S62')) {
            $result = 'Foxgate';
        } else if (strpos($system, 'Cisco')) {
            $result = 'Cisco';
        } else if (strpos($system, 'ROS')) {
            $result = 'ROS';
        } else if (strpos($system, 'NBA')) {
            $result = 'NBA';
        } else if (strpos($system, 'Layer 2 Management Switch')) {
            $result = 'Foxgate S6008';
        } else if (strpos($system, 'BDCOM')) {
            $result = 'BDCOM';
        }

        return $result;
    }

    public function getFdb($vendor)
    {
        $result = array();
        if (!empty($this->fdb))
        {
            $result = unserialize($this->fdb);
        }

        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 60000000, 1);
        $session->oid_increasing_check = false;

        if (!empty($this->fdbOid[$vendor]))
        {
            $oid = $this->fdbOid[$vendor];
        } else {
            $oid = $this->fdbOid['Unknown'];
        }

        $fdb = @$session->walk($oid);

        if (is_array($fdb)) {
            $result = array();
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

                $result[$mac] = $interface;
            }
        }
        $session->close();
        return $result;
    }
}
