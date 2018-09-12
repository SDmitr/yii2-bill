<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Switches;

/**
 * SwitchesSearch represents the model behind the search form about `app\models\Switches`.
 */
class SwitchesSearch extends Switches
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'aton', 'status_id'], 'integer'],
            [['name', 'vendor', 'ip'], 'safe'],
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
        $query = Switches::find()->joinWith(['status']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pagesize' => 10,
//            ]
            'sort'=> [
                'defaultOrder' => [
                    'aton' => SORT_ASC,
                ]
            ]
        ]);

        $dataProvider->sort->attributes['ip'] = [
            'asc' => ['{{%switches}}.aton' => SORT_ASC],
            'desc' => ['{{%switches}}.aton' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '{{%switches}}.id' => $this->id,
            '{{%switches}}.aton' => $this->aton,
            '{{%switches}}.status_id' => $this->status_id,
        ]);

        $query->andFilterWhere(['like', '{{%switches}}.name', $this->name]);
        $query->andFilterWhere(['like', '{{%switches}}.vendor', $this->vendor]);
        $query->andFilterWhere(['like', '{{%switches}}.ip', $this->ip]);

        return $dataProvider;
    }
}
