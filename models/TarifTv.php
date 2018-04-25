<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tarif_tv".
 *
 * @property integer $id
 * @property string $name
 * @property string $money
 *
 * @property Tv[] $tvs
 */
class TarifTv extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tarif_tv';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
            'name' => 'Name',
            'money' => 'Money',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTvs()
    {
        return $this->hasMany(Tv::className(), ['tarif_id' => 'id']);
    }
}
