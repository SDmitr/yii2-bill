<?php

namespace app\models;

use Yii;
use \SNMP;

/**
 * This is the model class for table "street".
 *
 * @property integer $id
 * @property string $name
 * @property string $vendor
 * @property string $oid
 * @property string $ip
 * @property string $interfaces
 * @property string $fdb
 *
 * @property Switches[] $switches
 */
class Switches extends \yii\db\ActiveRecord
{
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
        $result = array();
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $interfaces = unserialize($this->interfaces);

        if (!empty($interfaces))
        {
            foreach ($interfaces as $id => $name)
            {
                $status = @$session->get('1.3.6.1.2.1.2.2.1.8.' . $id);
                if ($session->getError()) throw new \Exception ($session->getError());
                $interfaceStatus = preg_replace('/\D/', '', $status);
                $result[$id] = $interfaceStatus;
            }
        }

        $session->close();
        return $result;
    }

    public function getInterfacesName()
    {
        $result = array();
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 1000000, 1);
        $session->oid_increasing_check = false;
        $interfaces = @$session->walk('1.3.6.1.2.1.2.2.1.3');

        if ($session->getError()) throw new \Exception ($session->getError());

        if (!empty($interfaces))
        {
            foreach ($interfaces as $oid => $interface)
            {
                $components = explode('.', $oid);
                $id = array_pop($components);
                $interfaceType = preg_replace('/\D/', '', $interface);
                if ($interfaceType == 6 || $interfaceType == 1)
                {
                    $name = @$session->get('1.3.6.1.2.1.2.2.1.2.' . $id);
                    preg_match('~\w+\:\s\"(.+)\"~', $name, $interfaceName);
                    $result[$id] = !empty($interfaceName[1]) ? $interfaceName[1] : $id;
                }
            }
        }

        $session->close();
        return $result;
    }

    public function getVendor()
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

    public function getFdb($vendor)
    {
        $result = array();
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
