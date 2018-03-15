<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 04.03.18
 * Time: 15:38
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ReplenishmentSearch extends Replenishment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','token','replenishment_at','amount','created_at'], 'safe'],
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
        $query = Replenishment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
            'sort'=> [
                'defaultOrder' => ['created_at'=>SORT_DESC],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}