<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Customers;


/**
 * CustomersSearch represents the model behind the search form about `backend\models\Customers`.
 */
class CustomersSearch extends Customers
{
    public $input_search;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sex','title', 'zipcode'], 'safe'],
            [['firstname','lastname','company','address','city','country','telephone','email', 'input_search'], 'safe'],
            [['id', 'corporatecard'], 'safe'],
            [['dob','created_at','updated_at'], 'safe'],
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
        $query = Customers::find();
        if (empty($this->input_search)) $query->where(['id' => -1]);
            /*->select([
                '{{CUSTOMERS}}.*', // select all customer fields
                '{{ORDERS}}.id AS orderId' // calculate orders count
            ])
            ->joinWith('orders') // ensure table junction
            ->all()*/;
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => Yii::$app->session->get('rows') ],
            'sort'=> [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
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
        ]);

        $query->orFilterWhere(['like', 'firstname', $this->input_search])
            ->orFilterWhere(['like', 'lastname', $this->input_search])
            ->orFilterWhere(['like', 'concat(lastname, \' \', firstname)', $this->input_search])
            ->orFilterWhere(['like', 'concat(firstname, \' \', lastname)', $this->input_search])
            ->orFilterWhere(['like', 'email', $this->input_search])
            ->orFilterWhere(['like', 'address', $this->input_search])
            ->orFilterWhere(['like', 'city', $this->input_search])
            ->orFilterWhere(['like', 'zipcode', $this->input_search])
            ->orFilterWhere(['like', 'telephone', $this->input_search])
            ->orFilterWhere(['like', 'dob', $this->input_search])
            ->orFilterWhere(['like', 'country', $this->input_search]);

        return $dataProvider;
    }
}
