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
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'inet_id', 'tarif_id', 'status_id'], 'integer'],
            [['date_on', 'date_off', 'date_create'], 'safe'],
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
        $query = Tv::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'inet_id' => $this->inet_id,
            'tarif_id' => $this->tarif_id,
            'status_id' => $this->status_id,
            'date_on' => $this->date_on,
            'date_off' => $this->date_off,
            'date_create' => $this->date_create,
        ]);

        return $dataProvider;
    }
}
