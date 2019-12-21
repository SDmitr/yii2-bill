<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PonLast;

/**
 * PonSearch represents the model behind the search form about `app\models\PonLast`.
 */
class PonLastSearch extends PonLast
{
    public $num;
    public $name;
    public $street;
    public $building;
    public $room;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['distance'], 'integer'],
            [['mac', 'host', 'interface', 'reason', 'date'], 'safe'],
            [['olt_power', 'onu_power', 'transmitted_power', 'temperature_onu'], 'number'],
            [['num', 'name', 'street', 'building', 'room'], 'safe'],
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
        $query = PonLast::find()->joinWith(['client']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                 'pageSize' => false,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'olt_power' => SORT_ASC,
                ]
            ]
        ]);
        
        $dataProvider->sort->attributes['num'] = [
            'asc' => ['{{%client}}.num' => SORT_ASC],
            'desc' => ['{{%client}}.num' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['name'] = [
            'asc' => ['{{%client}}.name' => SORT_ASC],
            'desc' => ['{{%client}}.name' => SORT_DESC],
        ];
                
        $dataProvider->sort->attributes['street'] = [
            'asc' => ['{{%client}}.street' => SORT_ASC],
            'desc' => ['{{%client}}.street' => SORT_DESC],
        ];
                        
        $dataProvider->sort->attributes['building'] = [
            'asc' => ['{{%client}}.building' => SORT_ASC],
            'desc' => ['{{%client}}.building' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['room'] = [
            'asc' => ['{{%client}}.room' => SORT_ASC],
            'desc' => ['{{%client}}.room' => SORT_DESC],
        ];
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '{{%pon_last}}.date' => $this->date,
            '{{%client}}.num' => $this->num
        ]);

        $query->andFilterWhere(['like', '{{%pon_last}}.mac', $this->mac])
            ->andFilterWhere(['like', '{{%pon_last}}.host', $this->host])
            ->andFilterWhere(['like', '{{%pon_last}}.interface', $this->interface])
            ->andFilterWhere(['like', '{{%pon_last}}.reason', $this->reason])
            ->andFilterWhere(['like', '{{%pon_last}}.olt_power', $this->olt_power])
            ->andFilterWhere(['like', '{{%pon_last}}.onu_power', $this->onu_power])
            ->andFilterWhere(['like', '{{%pon_last}}.transmitted_power', $this->transmitted_power])
            ->andFilterWhere(['like', '{{%pon_last}}.temperature_onu', $this->temperature_onu])
            ->andFilterWhere(['like', '{{%pon_last}}.distance', $this->distance])
//            ->andFilterWhere(['like', '{{%client}}.num', $this->num])
            ->andFilterWhere(['like', '{{%client}}.name', $this->name])
            ->andFilterWhere(['like', '{{%client}}.street', $this->street])
            ->andFilterWhere(['like', '{{%client}}.building', $this->building])
            ->andFilterWhere(['like', '{{%client}}.room', $this->room]);

        return $dataProvider;
    }
}
