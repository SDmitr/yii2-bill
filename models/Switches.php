<?php

namespace app\models;

use app\models\switches\Bdcom;
use app\models\switches\Dcn;
use app\models\switches\FoxgateS;
use Yii;
use \SNMP;

use app\models\switches\Eltex;
use app\models\switches\Nexthop;
use app\models\switches\Edgecore;
use app\models\switches\Cisco;
use app\models\switches\Foxgate;
use app\models\switches\Huawei;
use app\models\switches\Nba;
use app\models\switches\Raisecom;

/**
 * This is the model class for table "switches".
 *
 * @property integer $id
 * @property string $name
 * @property string $vendor
 * @property integer $aton
 * @property string $ip
 * @property string $interfaces
 * @property string $onus
 * @property string $fdb
 * @property integer $status_id
 *
 * @property Switches[] $switches
 */
class Switches extends \yii\db\ActiveRecord
{
    const STATUS_UP = 1;
    const STATUS_DOWN = 2;

    const INTERFACE_ACCESS = 1;
    const INTERFACE_TRUNK = 2;

    const INTERFACE_TYPE = array(6);

    const OID_SYSTEM = '1.3.6.1.2.1.1.1.0';
    const OID_NAME = '1.3.6.1.2.1.1.5.0';
    const OID_INTERFACES = '1.3.6.1.2.1.2.2.1.3';
    const OID_INTERFACE_NAME = '1.3.6.1.2.1.2.2.1.2.';
    const OID_INTERFACE_STATUS = '1.3.6.1.2.1.2.2.1.8.';
    const OID_INTERFACE_ADMIN_STATUS = '1.3.6.1.2.1.2.2.1.7.';
    const OID_INTERFACE_TYPE = '1.3.6.1.2.1.2.2.1.3.';
    const OID_INTERFACE_VLAN_MODE = '1.3.6.1.2.1.17.7.1.4.5.1.1.';
    const OID_FDB = '1.3.6.1.2.1.17.7.1.2.2.1.2';

    const VENDOR = 'Unknown';

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
            [['name', 'aton', 'ip'], 'required'],
            [['aton', 'status_id'], 'integer'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
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
            'aton' => 'Aton',
            'ip' => 'IP-адрес',
            'interfaces' => 'Интерфейсы',
	        'onus' => 'ONU',
            'fdb' => 'MAC-таблица',
            'status_id' => 'Статус'
        ];
    }

    /**
     * @throws \Exception
     */
    public static function getSwitchEntity($ip)
    {
        $session = new SNMP(SNMP::VERSION_2c, $ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $system = @$session->get(static::OID_SYSTEM);
        if ($session->getError()) throw new \Exception ($session->getError());
        @$session->close();

        if (strpos($system, 'NH-') || strpos($system, 'Hex-STRING') === 0 || strpos($system, 'Internetwork Operating System')) {
            $model = new Nexthop();
        } elseif (strpos($system, 'MES')) {
            $model = new Eltex();
        } elseif (strpos($system, 'ES') || strpos($system, 'managed standalone switch')) {
            $model = new Edgecore();
        } elseif (strpos($system, 'Huawei')) {
            $model = new Huawei();
        } elseif (strpos($system, 'S62')) {
            $model = new Foxgate();
        } elseif (strpos($system, 'Cisco')) {
            $model = new Cisco();
        } elseif (strpos($system, 'ROS')) {
            $model = new Raisecom();
        } elseif (strpos($system, 'NBA')) {
            $model = new Nba();
        } elseif (strpos($system, 'Layer 2 Management Switch')) {
            $model = new FoxgateS();
        } elseif (strpos($system, 'BDCOM')) {
            $model = new Bdcom();
        } elseif (strpos($system, 'S4200') || strpos($system, 'S4600')) {
            $model = new Dcn();
        } else {
            $model = new Switches();
        }
        return $model;
    }

    /**
     *
     */
    public function setVendor()
    {
        $this->vendor = static::VENDOR;
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function setSwitchName()
    {
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $string = @$session->get(static::OID_NAME);
        @$session->close();

        preg_match('~\w+\:\s\"(\w+)\"~', $string, $switchName);

        if (empty($switchName[1])) {
            $name = $this->ip;
        } else {
            $name = $switchName[1];
        }

        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getInterfaces()
    {
        $result = array();
        $interfaces = @unserialize($this->interfaces);
        if (!empty($interfaces)) {
            foreach ($interfaces as $id => $item) {
                if (isset($item['type']) && $item['type'] == 6) {
                    try {
                        $result[$id]['status'] = $this->getInterfaceStatus($id);
                        $result[$id]['admin_status'] = $this->getInterfaceAdminStatus($id);
                        $result[$id]['vlan_mode'] = $item['vlan_mode'];
                        $result[$id]['name'] = $item['name'];
                    } catch (\Exception $e) {
                        foreach ($interfaces as $id => $item) {
                            $result[$id]['status'] = self::STATUS_DOWN;
                            $result[$id]['admin_status'] = self::STATUS_UP;
                            $result[$id]['vlan_mode'] = self::INTERFACE_ACCESS;
                            $result[$id]['name'] = '';
                        }
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @throws \Exception
     */
    public function setInterfaces()
    {
        $result = empty($this->interfaces) ? array() : @unserialize($this->interfaces);

        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 1000000, 1);
        $session->oid_increasing_check = false;
        $interfaces = @$session->walk(static::OID_INTERFACES);

        if (!empty($interfaces)) {
            $result = array();
            foreach ($interfaces as $oid => $interface) {
                $components = explode('.', $oid);
                $id = array_pop($components);

                $interfaceType = @$session->get( static::OID_INTERFACE_TYPE . $id);
                $interfaceType = preg_replace('/.+\:/', '', $interfaceType);
                if (in_array($interfaceType, static::INTERFACE_TYPE)) {
                    $name = @$session->get(static::OID_INTERFACE_NAME . $id);
                    preg_match('~\w+\:\s\"(.+)\"~', $name, $interfaceName);
                    $result[$id]['name'] = !empty($interfaceName[1]) ? $interfaceName[1] : $id;
                    $result[$id]['type'] = $interfaceType;
                    $result[$id]['vlan_mode'] = $this->setVlanMode($id);
                }
            }
        }
        @$session->close();

        $this->interfaces = @serialize($result);
    }

    /**
     * @param $id
     * @return mixed|string|string[]|null
     */
    public function getInterfaceStatus($id)
    {
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $status = @$session->get(static::OID_INTERFACE_STATUS. $id);
        @$session->close();
        $status = preg_replace('/\D/', '', $status);
        return $status;
    }

    /**
     * @param $id
     * @return mixed|string|string[]|null
     */
    public function getInterfaceAdminStatus($id)
    {
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $status = @$session->get(static::OID_INTERFACE_ADMIN_STATUS . $id);
        @$session->close();
        $status = preg_replace('/\D/', '', $status);
        return $status;
    }

    /**
     * @param $id
     * @return int
     */
    public function setVlanMode($id)
    {
        $result = self::INTERFACE_TRUNK;
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $vlan = @$session->get(static::OID_INTERFACE_VLAN_MODE . $id);
        @$session->close();
        preg_match('~\w+\:\s(\w+)~', $vlan, $vlanMode);
        if (isset($vlanMode[1]) && $vlanMode[1] > 1 && $vlanMode[1] != 1000 && $vlanMode[1] != 999) {
            $result = self::INTERFACE_ACCESS;
        } else if (!isset($vlanMode[1])) {
            $result = self::INTERFACE_ACCESS;
        }
        
        return $result;
    }

    /**
     *
     */
    public function setFdb()
    {
        $result = empty($this->fdb) ? array() : @unserialize($this->fdb);
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 60000000, 1);
        $session->oid_increasing_check = false;
        $fdb = @$session->walk(static::OID_FDB);
        @$session->close();

        if ($fdb && is_array($fdb)) {
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
        $this->fdb = @serialize($result);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    /**
     * @param $ip
     * @return mixed
     */
    public function findByIp($ip)
    {
        return static::findOne(array('ip' => $ip));
    }
}
