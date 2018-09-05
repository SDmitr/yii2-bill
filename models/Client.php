<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "client".
 *
 * @property integer $id
 * @property integer $num
 * @property string $name
 * @property string $street
 * @property string $building
 * @property string $room
 * @property string $phone_1
 * @property string $phone_2
 * @property string $email
 *
 * @property Group $group
 * @property Type $type
 * @property Inet[] $inets
 * @property Money[] $moneys
 * @property Tv[] $tvs
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num', 'name'], 'required'],
            [['num'], 'integer'],
            [['name', 'street', 'building', 'room', 'phone_1', 'phone_2', 'email'], 'string', 'max' => 255],
            [['num'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'num' => 'Договор',
            'name' => 'ФИО',
            'street' => 'Улица',
            'building' => 'Дом',
            'room' => 'Квартира',
            'phone_1' => 'Телефон 1',
            'phone_2' => 'Телефон 2',
            'email' => 'Email',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInets()
    {
        return $this->hasMany(Inet::className(), ['num' => 'num']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMoneys()
    {
        return $this->hasMany(Money::className(), ['num' => 'num']);
    }
}
