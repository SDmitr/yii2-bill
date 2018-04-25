<?php

namespace app\models\site;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property integer $id
 * @property string $action
 * @property string $ip
 * @property string $user
 * @property string $description
 * @property string $after
 * @property string $until
 * @property string $create_at
 * @property integer $level
 */
class Log extends \yii\db\ActiveRecord
{
    const INFO = 1;
    const WARNING = 2;
    const DANGER = 3;
    const SUCCESS = 4;
    
    const LOGIN = 'login';
    const LOGOUT = 'logout';
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const HACKING = 'hacking';
    const ENABLE = 'enable';
    const DISABLE = 'disable';
    const DHCP = 'dhcp';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip'], 'required'],
            [['create_at', 'level'], 'safe'],
            [['action', 'user', 'ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action' => 'Событие',
            'ip' => 'IP-адрес',
            'user' => 'Пользователь',
            'description' => 'Подробно',
            'after' => 'После изменений',
            'until' => 'До изменений',
            'create_at' => 'Дата',
            'level' => 'Уровень',
        ];
    }
    
    public function add($description, $action, $level, $model)
    {
        $this->action = $action;
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->user = isset(Yii::$app->user->getIdentity()->username) ? Yii::$app->user->getIdentity()->username : '';
        $this->description = $description;
        $this->level = $level;
        
        if ($this->action == self::HACKING || $this->action == self::LOGIN) {
            $this->user = $model->attributes['username'];
        } else {
            if ($this->action != self::DELETE) {
                $this->after = !empty($model->attributes) ? base64_encode(serialize($model->attributes)) : '';
            }
            if ($this->action != self::CREATE) {
                $this->until = !empty($model->oldAttributes) ? base64_encode(serialize($model->oldAttributes)) : '';
            }
            if ($this->action == self::ENABLE || $this->action == self::DISABLE) {
                $this->until = '';
                $this->after = !empty($model->attributes) ? base64_encode(serialize($model->attributes)) : '';
            }
            if ($this->action == self::DHCP) {
                $this->after = !empty($model) ? base64_encode(serialize($model)) : '';
            }
        }
        
        if ($this->action != self::UPDATE || $this->action != self::DELETE) {
            $this->save();
        }
    }
}
