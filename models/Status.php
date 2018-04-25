<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Inet[] $inets
 * @property Tv[] $tvs
 */
class Status extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'list'], 'required'],
            [['name', 'list'], 'string', 'max' => 255],
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
            'list' => 'Address-list',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInets()
    {
        return $this->hasMany(Inet::className(), ['status_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTvs()
    {
        return $this->hasMany(Tv::className(), ['status_id' => 'id']);
    }
}
