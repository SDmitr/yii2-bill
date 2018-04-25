<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "money".
 *
 * @property integer $id
 * @property integer $num
 *
 * @property Client $num0
 */
class Money extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'money';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num'], 'required'],
            [['num'], 'integer'],
            [['num'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['num' => 'num']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'num' => 'Num',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['num' => 'num']);
    }
}
