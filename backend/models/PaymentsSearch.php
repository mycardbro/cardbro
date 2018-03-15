<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Payments;

/**
 * PaymentsSearch represents the model behind the search form about `backend\models\Payments`.
 */
class PaymentsSearch extends Payments
{
    public $input_search;
    public $status;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status_id',  'token', 'type_id'], 'integer'],
            [['bill_amount', 'paid_amount',], 'number'],
            [['created_at', 'updated_at', 'paid_at', 'input_search',], 'safe'],
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
        $query = Payments::find();

        $query->joinWith(['status']);

        $query->joinWith(['types']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->session->get('rows'),
            ],
            'sort'=> [
                'defaultOrder' => ['updated_at'=>SORT_DESC, 'token' => SORT_DESC],
            ]
        ]);

        $dataProvider->sort->attributes['status'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['status.name' => SORT_ASC],
            'desc' => ['status.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['types'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['types.name' => SORT_ASC],
            'desc' => ['types.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->orFilterWhere(['like', 'token', $this->input_search])
            ->orFilterWhere(['like', 'bill_amount', $this->input_search])
            ->orFilterWhere(['like', 'paid_amount', $this->input_search])
            ->orFilterWhere(['like', 'status.name', $this->input_search])
            ->orFilterWhere(['like', 'types.name', $this->input_search]);

        return $dataProvider;
    }
}
