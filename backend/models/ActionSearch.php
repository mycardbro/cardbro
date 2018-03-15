<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 08.01.18
 * Time: 14:29
 */

namespace backend\models;

use yii\data\ActiveDataProvider;
use Yii;

class ActionSearch extends Action
{
    public $input_search;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','user_id','ip','type_id','product_id','amount', 'created_at', 'input_search'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Action::find();
        $query->joinWith(['user', 'type', 'product']);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => Yii::$app->session->get('rows') ],
            'sort'=> [
                'defaultOrder' => ['created_at'=>SORT_DESC],
            ],
        ]);

        $dataProvider->sort->attributes['email'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['user.email' => SORT_ASC],
            'desc' => ['user.email' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['product'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['product.name' => SORT_ASC],
            'desc' => ['product.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['type'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['action_type.name' => SORT_ASC],
            'desc' => ['action_type.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['or',
            ['like', 'user.email', $this->input_search],
            ['like', 'action_type.name', $this->input_search],
            ['like', 'product.name', $this->input_search],
        ]);

        return $dataProvider;
    }
}