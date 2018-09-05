<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "street".
 *
 * @property integer $id
 * @property string $name
 * @property string $vendor
 * @property string $oid
 * @property string $ip
 * @property string $interface_count
 * @property string $interface_status
 * @property string $fdb
 *
 * @property Switches[] $switches
 */
class Switches extends \yii\db\ActiveRecord
{
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
            [['name'], 'required'],
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
            'oid' => 'OID',
            'ip' => 'IP-адрес',
            'interface_count' => 'Количество портов',
            'interface_status' => 'Статус портов',
            'fdb' => 'MAC-таблица',
        ];
    }
}
