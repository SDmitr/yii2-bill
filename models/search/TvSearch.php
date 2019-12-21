<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tv;

/**
 * TvSearch represents the model behind the search form of `app\models\Tv`.
 */
class TvSearch extends Tv
{
    public $num;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'inet_id', 'tarif_id', 'status_id'], 'integer'],
            [['date_on', 'date_off', 'date_create'], 'safe'],
            [['num'], 'safe'],
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
        $query = Tv::find()->joinWith(['inet', 'tarif', 'status']);

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
            '{{%tv}}.id' => $this->id,
            '{{%inet}}.num' => $this->num,
            '{{%tv}}.inet_id' => $this->inet_id,
            '{{%tv}}.tarif_id' => $this->tarif_id,
            '{{%tv}}.status_id' => $this->status_id,
            '{{%tv}}.date_on' => $this->date_on,
            '{{%tv}}.date_off' => $this->date_off,
            '{{%tv}}.date_create' => $this->date_create,
        ]);
        
        $query->andFilterWhere(['like', '{{%tv}}.date_create', $this->date_create]);

        return $dataProvider;
    }
}
