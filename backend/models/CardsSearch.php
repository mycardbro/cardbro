<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Cards;

/**
 * CardsSearch represents the model behind the search form about `backend\models\Cards`.
 */
class CardsSearch extends Cards
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ID', 'PRODUCTID', 'TOKEN'], 'integer'],
            [['CREATED_DATE', 'ACTIVATION_DATE', 'STATUS_NAME'], 'safe'],
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
        $query = Cards::find();

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
            'ID' => $this->ID,
            'PRODUCTID' => $this->PRODUCTID,
            'ORDER_ID' => $this->ORDER_ID,
        ]);

        $query->andFilterWhere(['like', 'TOKEN', $this->TOKEN])
            ->andFilterWhere(['like', 'ACTIVATION_DATE', $this->ACTIVATION_DATE])
            ->andFilterWhere(['like', 'CREATED_DATE', $this->CREATED_DATE])
            ->andFilterWhere(['like', 'STATUS_NAME', $this->STATUS_NAME]);

        return $dataProvider;
    }
}
