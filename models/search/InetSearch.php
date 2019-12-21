<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Inet;

/**
 * InetSearch represents the model behind the search form about `app\models\Inet`.
 */
class InetSearch extends Inet
{
    public $client;
    public $street;
    public $building;
    public $room;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'num', 'aton', 'tarif_id', 'status_id'], 'integer'],
            [['ip', 'mac', 'switch', 'interface', 'onu_mac', 'date_on', 'date_off', 'date_create'], 'safe'],
            [['client', 'street', 'building', 'room'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Inet::find()->joinWith(['client', 'tarif', 'status', 'tv', 'switches']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pagesize' => 10,
//            ],
            'sort'=> [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        
        $dataProvider->sort->attributes['client'] = [
            'asc' => ['client.name' => SORT_ASC],
            'desc' => ['client.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['street'] = [
            'asc' => ['client.street' => SORT_ASC],
            'desc' => ['client.street' => SORT_DESC],
        ];
                
        $dataProvider->sort->attributes['building'] = [
            'asc' => ['client.building' => SORT_ASC],
            'desc' => ['client.building' => SORT_DESC],
        ];
                        
        $dataProvider->sort->attributes['room'] = [
            'asc' => ['client.room' => SORT_ASC],
            'desc' => ['client.room' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['ip'] = [
            'asc' => ['{{%inet}}.aton' => SORT_ASC],
            'desc' => ['{{%inet}}.aton' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '{{%inet}}.id' => $this->id,
            '{{%inet}}.num' => $this->num,
            '{{%inet}}.aton' => $this->aton,
            '{{%inet}}.interface' => $this->interface,
            '{{%inet}}.tarif_id' => $this->tarif_id,
            '{{%inet}}.status_id' => $this->status_id,
            '{{%inet}}.switch'  => $this->switch
//            '{{%inet}}.date_on' => $this->date_on,
//            '{{%inet}}.date_off' => $this->date_off,
//            '{{%inet}}.date_create' => $this->date_create,
        ]);

        $query->andFilterWhere(['like', '{{%inet}}.ip', $this->ip])
            ->andFilterWhere(['like', '{{%inet}}.mac', $this->mac])
//            ->andFilterWhere(['like', '{{%inet}}.switch', $this->switch])
            ->andFilterWhere(['like', '{{%inet}}.onu_mac', $this->onu_mac])
//            ->andFilterWhere(['like', '{{%inet}}.num', $this->num])
            ->andFilterWhere(['like', '{{%inet}}.date_on', $this->date_on])
            ->andFilterWhere(['like', '{{%inet}}.date_off', $this->date_off])
            ->andFilterWhere(['like', '{{%inet}}.date_create', $this->date_create])
            ->andFilterWhere(['like', '{{%client}}.name', $this->client])
            ->andFilterWhere(['like', '{{%client}}.street', $this->street])
            ->andFilterWhere(['like', '{{%client}}.building', $this->building])
            ->andFilterWhere(['like', '{{%client}}.room', $this->room]);
            

        return $dataProvider;
    }
}
