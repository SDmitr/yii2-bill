<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pon_last".
 *
 * @property string $mac
 * @property string $host
 * @property string $interface
 * @property string $olt_power
 * @property string $onu_power
 * @property string $transmitted_power
 * @property string $temperature_onu
 * @property integer $distance
 * @property string $reason
 * @property string $date
 */
class PonLast extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pon_last';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mac', 'host'], 'required'],
            [['olt_power', 'onu_power', 'transmitted_power', 'temperature_onu'], 'number'],
            [['distance'], 'integer'],
            [['date'], 'safe'],
            [['mac', 'host', 'interface', 'reason'], 'string', 'max' => 255],
            [['mac'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mac' => 'MAC-адрес ONU',
            'host' => 'OLT',
            'interface' => 'PON-интерфейс',
            'olt_power' => 'Уровень на OLT',
            'onu_power' => 'Уровень на ONU',
            'transmitted_power' => 'Мощность передатчика',
            'temperature_onu' => 'Температура ONU',
            'distance' => 'Расстояние',
            'reason' => 'Состояние',
            'date' => 'Дата',
        ];
    }
    
    public function getClient()
    {
        $result = $this->hasOne(Client::className(), ['num' => 'num'])->viaTable('{{%inet}}', ['onu_mac' => 'mac']);
        return $result ? $result : '';
    }
    
    public function getPon()
    {
        return $this->hasOne(PonLast::className(), ['mac' => 'mac']);
    }
}
