<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tarif_inet".
 *
 * @property integer $id
 * @property string $name
 * @property integer $speed
 * @property string $money
 *
 * @property Inet[] $inets
 */
class TarifInet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tarif_inet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'speed'], 'required'],
            [['speed'], 'integer'],
            [['name', 'money'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Тариф',
            'speed' => 'Скорость, Мб/с',
            'money' => 'Абонплата, грн',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInets()
    {
        return $this->hasMany(Inet::className(), ['tarif_id' => 'id']);
    }
}
