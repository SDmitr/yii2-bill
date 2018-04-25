<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pon".
 *
 * @property integer $id
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
class Pon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mac', 'host'], 'required'],
            [['olt_power', 'onu_power', 'transmitted_power', 'temperature_onu', 'distance'], 'number'],
            [['date'], 'safe'],
            [['mac', 'host', 'interface', 'reason'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mac' => 'Mac',
            'host' => 'Host',
            'interface' => 'Interface',
            'olt_power' => 'Olt Power',
            'onu_power' => 'Onu Power',
            'transmitted_power' => 'Transmitted Power',
            'temperature_onu' => 'Temperature Onu',
            'distance' => 'Distance',
            'reason' => 'Reason',
            'date' => 'Date',
        ];
    }
}
