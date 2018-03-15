<?php
namespace backend\models;

use backend\models\UserProductRelations;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserProductRelationsSearch extends UserProductRelations
{
    public function search($params)
    {
        $query = UserProductRelations::find();

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
        ]);

        //$query->andFilterWhere(['like', 'INVOICE_ID', $this->INVOICE_ID]);

        return $dataProvider;
    }
}