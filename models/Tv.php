<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tv".
 *
 * @property int $id
 * @property int $inet_id
 * @property int $tarif_id
 * @property int $status_id
 * @property string $date_on
 * @property string $date_off
 * @property string $date_create
 *
 * @property Inet $inet
 * @property Status $status
 * @property TarifTv $tarif
 */
class Tv extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tv';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inet_id'], 'required'],
            [['inet_id', 'tarif_id', 'status_id'], 'integer'],
            [['date_on', 'date_off', 'date_create'], 'safe'],
            [['inet_id'], 'exist', 'skipOnError' => true, 'targetClass' => Inet::className(), 'targetAttribute' => ['inet_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['tarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => TarifTv::className(), 'targetAttribute' => ['tarif_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inet_id' => 'Inet ID',
            'tarif_id' => 'Тариф',
            'status_id' => 'Статус',
            'date_on' => 'Date On',
            'date_off' => 'Date Off',
            'date_create' => 'Дата создания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInet()
    {
        return $this->hasOne(Inet::className(), ['id' => 'inet_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTarif()
    {
        return $this->hasOne(TarifTv::className(), ['id' => 'tarif_id']);
    }
}
