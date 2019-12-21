<?php

namespace app\models;

use Yii;
use \SNMP;
use app\models\Status;

/**
 * This is the model class for table "switches".
 *
 * @property integer $id
 * @property string $name
 * @property string $vendor
 * @property integer $aton
 * @property string $ip
 * @property string $interfaces
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

    private $localMacOid = array(
        'Nexthop'       => '1.3.6.1.2.1.17.1.1.0',
        'Foxgate'       => '1.3.6.1.2.1.17.1.1.0',
        'Huawei'        => '1.3.6.1.2.1.17.1.1.0',
        'Foxgate S6008' => '1.3.6.1.2.1.17.1.1.0',
        'Cisco'         => '1.3.6.1.2.1.17.1.1.0',
        'ROS'           => '1.3.6.1.2.1.17.1.1.0',
        'Edge-core'     => '1.3.6.1.2.1.17.1.1.0',
        'BDCOM'         => '1.3.6.1.2.1.17.1.1.0',
        'Unknown'       => '1.3.6.1.2.1.17.1.1.0',
    );


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
            'fdb' => 'MAC-таблица',
            'status_id' => 'Статус'
        ];
    }

    public function setSwitchName()
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
        $this->name = $name;
    }

    public function getInterfacesStatus()
    {
        $result = array();
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

    public function setInterfaces()
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
                    $result[$id]['status'] = $this->setInterfaceStatus($id);
                    $result[$id]['vlan_mode'] = $this->setVlanMode($id);
                }
            }
        }

        $session->close();
        $this->interfaces = serialize($result);
    }
    
    public function setInterfaceStatus($id)
    {
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $status = @$session->get('1.3.6.1.2.1.2.2.1.7.' . $id);
        $status = preg_replace('/\D/', '', $status);
        
        return $status;
    }
    
    public function setVlanMode($id)
    {
        $result = self::INTERFACE_TRUNK;
        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 500000, 1);
        $session->oid_increasing_check = false;
        $vlan = @$session->get('1.3.6.1.2.1.17.7.1.4.5.1.1.' . $id);
        preg_match('~\w+\:\s(\w+)~', $vlan, $vlanMode);
        if (isset($vlanMode[1]) && $vlanMode[1] > 1 && $vlanMode[1] != 1000 && $vlanMode[1] != 999) {
            $result = self::INTERFACE_ACCESS;
        }
        
        return $result;
    }

    public function setVendor()
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

        if (strpos($system, 'NH-') || strpos($system, 'Hex-STRING') === 0 || strpos($system, 'Internetwork Operating System')) {
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

        $this->vendor = $result;
    }

    public function setFdb()
    {
        $result = array();
        if (!empty($this->fdb))
        {
            $result = unserialize($this->fdb);
        }

        $session = new SNMP(SNMP::VERSION_2c, $this->ip, Yii::$app->params['managementNetwork']['snmpCommunity'], 60000000, 1);
        $session->oid_increasing_check = false;

        if (!empty($this->fdbOid[$this->vendor]))
        {
            $oid = $this->fdbOid[$this->vendor];
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
        $this->fdb = serialize($result);
    }

    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }
}
