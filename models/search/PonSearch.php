<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pon;

/**
 * PonSearch represents the model behind the search form about `app\models\Pon`.
 */
class PonSearch extends Pon
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'distance'], 'integer'],
            [['mac', 'host', 'interface', 'reason', 'date'], 'safe'],
            [['olt_power', 'onu_power', 'transmitted_power', 'temperature_onu'], 'number'],
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
        $query = Pon::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pagesize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'olt_power' => SORT_ASC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'olt_power' => $this->olt_power,
            'onu_power' => $this->onu_power,
            'transmitted_power' => $this->transmitted_power,
            'temperature_onu' => $this->temperature_onu,
            'distance' => $this->distance,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'mac', $this->mac])
            ->andFilterWhere(['like', 'host', $this->host])
            ->andFilterWhere(['like', 'interface', $this->interface])
            ->andFilterWhere(['like', 'reason', $this->reason]);

        return $dataProvider;
    }
}
