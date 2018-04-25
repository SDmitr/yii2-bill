<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "network".
 *
 * @property integer $id
 * @property string $name
 * @property string $first_ip
 * @property string $last_ip
 */
class Network extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'network';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'first_ip', 'last_ip', 'gateway', 'subnet', 'mask', 'dns1', 'dns2'], 'required'],
            [['name', 'first_ip', 'last_ip', 'gateway', 'subnet', 'mask', 'dns1', 'dns2'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['first_ip'], 'unique'],
            [['last_ip'], 'unique'],
            [['subnet'], 'unique'],
            [['gateway'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Локация',
            'first_ip' => 'Начальный IP-адрес',
            'last_ip' => 'Конечный IP-адрес',
            'subnet' => 'Адрес сети',
            'mask' => 'Маска',
            'gateway' => 'Шлюз',
            'dns1' => 'DNS Primary',
            'dns2' => 'DNS Secondary'
        ];
    }
    
    public function getNetworkId($aton)
    {
        $networks = $this->find()->all();
        
        foreach ($networks as $network) {
            if ($aton >= ip2long($network->attributes['first_ip']) && $aton <= ip2long($network->attributes['last_ip'])) {
                return $network->attributes['id'];
            }
        }
        return false;
    }
}
