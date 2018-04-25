<?php

namespace app\models;

use Yii;
use app\models\site\Log;

/**
 * This is the model class for table "inet".
 *
 * @property integer $id
 * @property integer $num
 * @property string $ip
 * @property integer $aton
 * @property string $mac
 * @property string $comment
 * @property string $switch
 * @property string $interface
 * @property integer $tarif_id
 * @property integer $status_id
 * @property string $onu_mac
 * @property string $date_on
 * @property string $date_off
 * @property string $date_create
 *
 * @property Client $client
 * @property Speed $speed
 * @property Status $status
 * @property TarifInet $tarif
 */
class Inet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['num', 'ip', 'aton', 'mac'], 'required'],
            [['num', 'aton', 'tarif_id', 'status_id'], 'integer'],
            [['date_on', 'date_off', 'date_create'], 'safe'],
            [['ip', 'mac', 'comment', 'switch', 'interface'], 'string', 'max' => 255],
            [['ip'], 'unique'],
            [['aton'], 'unique'],
            ['ip', 'validateIp'],
            [['mac'], 'unique'],
            ['mac' , 'validateMac'],
            ['onu_mac', 'validateMac'],
            [['num'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['num' => 'num']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['tarif_id'], 'exist', 'skipOnError' => true, 'targetClass' => TarifInet::className(), 'targetAttribute' => ['tarif_id' => 'id']],
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
            'ip' => 'IP-адрес',
            'aton' => 'Aton',
            'mac' => 'MAC-адрес',
            'comment' => 'Комментарий',
            'switch' => 'Коммутатор',
            'interface' => 'Порт',
            'tarif_id' => 'Тариф',
            'status_id' => 'Статус',
            'onu_mac' => 'ONU',
            'date_on' => 'Дата включения',
            'date_off' => 'Дата отключения',
            'date_create' => 'Дата создания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['num' => 'num']);
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
        return $this->hasOne(TarifInet::className(), ['id' => 'tarif_id']);
    }
    
    use \dixonstarter\togglecolumn\ToggleActionTrait;
    public function getToggleItems()
    {
        // custom array for toggle update
        return  [
            'on' => ['value'=>'1', 'label'=>'Включен'],
            'off' => ['value'=>'2', 'label'=>'Отключен'],
        ];
    }
    
    public function validateMac($attribute)
    {
        if (!empty($this->$attribute)) {
            $pattern = '/^[0-9a-fA-F]{2}[\-: ]{1}[0-9a-fA-F]{2}[\-: ]{1}[0-9a-fA-F]{2}[\-: ]{1}[0-9a-fA-F]{2}[\-: ]{1}[0-9a-fA-F]{2}[\-: ]{1}[0-9a-fA-F]{2}$/i';
            if (!preg_match($pattern, $this->$attribute)) {
                $this->addError($attribute, 'Некорректный MAC-адрес');
            }
        }
    }
    
    public function validateIp($attribute)
    {
        if (!empty($this->$attribute)) {
            if (ip2long($this->$attribute) == false) {
                $this->addError($attribute, 'Некорректный IP-адрес');
            }
        }
    }
    
    public function beforeSave($insert)
    {
        $mikrotik = Yii::$app->params['Mikrotik'];
        $api = new RouterosAPI();
        if ($api->connect($mikrotik['address'], $mikrotik['login'], $mikrotik['password']) && empty($insert)) {
            $isPresent = true;
            while ($isPresent) {
                $result = $api->comm('/ip/firewall/address-list/print');
                foreach ($result as $item) {
                    if (($item['address'] == $this->oldAttributes['ip'] || $item['address'] == $this->oldAttributes['ip']) && $item['list'] != $mikrotik['blacklist']) {
                        $api->comm('/ip/firewall/address-list/remove', array('.id' => $item['.id']));
                        $isPresent = true;
                        break;
                    } else {
                        $isPresent = false;
                    }
                }
            }
            $api->disconnect();
        }
        
        if (parent::beforeSave($insert)) {
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $mikrotik = Yii::$app->params['Mikrotik'];
        $api = new RouterosAPI();
        if ($api->connect($mikrotik['address'], $mikrotik['login'], $mikrotik['password'])) {
            $api->comm('/ip/firewall/address-list/add', array('list' => $this->status->list, 'address' => $this->ip, 'comment' => 'user ' . $this->num));
            $api->comm('/ip/firewall/address-list/add', array('list' => $this->tarif->speed . 'M', 'address' => $this->ip, 'comment' => 'user ' . $this->num));
            $api->disconnect();
        }
        
        if (Yii::$app->controller->id != 'import') {
            if (isset($changedAttributes['ip']) || isset($changedAttributes['mac']) || $insert) {
                $dhcp = \Yii::$app->runAction('dhcp/create');
            }
        }
        
        if (isset($changedAttributes['status_id']) && count($changedAttributes) == 1) {
            $log = new Log();
            if ($this->status_id == 1) {
                $log->add('Подключение включено', Log::ENABLE, Log::SUCCESS, $this);     
            } elseif ($this->status_id == 2) {
                $log->add('Подключение выключено', Log::DISABLE, Log::DANGER, $this);
            }
        }
        
        parent::afterSave($insert, $changedAttributes);
    }
}
