<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Client;

/**
 * ClientSearch represents the model behind the search form about `app\models\Client`.
 */
class ClientSearch extends Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'num'], 'integer'],
            [['name', 'street', 'building', 'room', 'phone_1', 'phone_2', 'email'], 'safe'],
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
        $query = Client::find();

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

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '{{%client}}.id' => $this->id,
            '{{%client}}.num' => $this->num
        ]);

        $query->andFilterWhere(['like', '{{%client}}.name', $this->name])
            ->andFilterWhere(['like', '{{%client}}.street', $this->street])
            ->andFilterWhere(['like', '{{%client}}.building', $this->building])
            ->andFilterWhere(['like', '{{%client}}.room', $this->room])
            ->andFilterWhere(['like', '{{%client}}.phone_1', $this->phone_1])
            ->andFilterWhere(['like', '{{%client}}.phone_2', $this->phone_2])
            ->andFilterWhere(['like', '{{%client}}.email', $this->email]);
//            ->andFilterWhere(['like', '{{%client}}.num', $this->num]);

        return $dataProvider;
    }
}
