<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Orders;

/**
 * OrdersSearch represents the model behind the search form about `app\models\Orders`.
 */
class OrdersSearch extends Orders
{
    public $input_search;
    public $date_from;
    public $date_to;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'token', 'customer_id', 'status_id'], 'integer'],
            [['pull_date', 'creation_date', 'activation_date', 'input_search', 'date_from','date_to'], 'safe'],
            [['invoice_id', 'card_name', 'comment', 'recid'], 'string'],
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
        $query = Orders::find();
        $query->joinWith(['customer', 'status', 'invoice.product']);
        // add conditions that should always apply here
	    if (Yii::$app->user->can('partner') && !Yii::$app->user->can('admin')) {
            $invoices = Invoice::find()->select('id')->where(['user_id' => Yii::$app->user->id])->all();
            $ids = [];
            foreach ($invoices as $invoice) {
                $ids[] = $invoice->id;
            }

		    $query->where(['in', 'invoice_id', $ids]);
	    }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => Yii::$app->session->get('rows') ],
            'sort'=> [
                'defaultOrder' => ['updated_at' => SORT_DESC],
            ],
        ]);

        $dataProvider->sort->attributes['status'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['status.name' => SORT_ASC],
            'desc' => ['status.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['firstname'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['customer.firstname' => SORT_ASC],
            'desc' => ['customer.firstname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['lastname'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['customer.lastname' => SORT_ASC],
            'desc' => ['customer.lastname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['email'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['customer.email' => SORT_ASC],
            'desc' => ['customer.email' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['productname'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['product.name' => SORT_ASC],
            'desc' => ['product.name' => SORT_DESC],
        ];

        /*$params['dateFrom'] = (!isset($params['dateFrom'])) ? '2015-01-01' : $params['dateFrom'];
        $params['dateTo'] = (!isset($params['dateTo'])) ? '2018-01-01' : $params['dateTo'];*/

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        /*$query->andFilterWhere([
            'ID' => $this->ID,
            'CUSTOMERS_ID' => $this->CUSTOMERS_ID,
            'PRODUCT_ID' => $this->PRODUCT_ID,
            'COUNTRY_CODE' => $this->COUNTRY_CODE,
            'REDIRECTED' => $this->REDIRECTED,
            'TIME_STAMP' => $this->TIME_STAMP,
            'REQUESTED_AMTLOAD' => $this->REQUESTED_AMTLOAD,
        ]);

        $query->andFilterWhere(['like', 'INVOICE_ID', $this->INVOICE_ID])
            ->andFilterWhere(['like', 'SEX', $this->SEX])
            ->andFilterWhere(['like', 'TITLE', $this->TITLE])
            ->andFilterWhere(['like', 'FIRSTNAME', $this->FIRSTNAME])
            ->andFilterWhere(['like', 'LASTNAME', $this->LASTNAME])
            ->andFilterWhere(['like', 'COMPANY', $this->COMPANY])
            ->andFilterWhere(['like', 'ADDRESS', $this->ADDRESS])
            ->andFilterWhere(['like', 'ZIPCODE', $this->ZIPCODE])
            ->andFilterWhere(['like', 'CITY', $this->CITY])
            ->andFilterWhere(['like', 'TELEPHONE', $this->TELEPHONE])
            ->andFilterWhere(['like', 'DOB', $this->DOB])
            ->andFilterWhere(['like', 'MAIL', $this->MAIL])
            ->andFilterWhere(['like', 'IP_STAMP', $this->IP_STAMP])
            ->andFilterWhere(['like', 'ACTIVATION_NUMBER', $this->ACTIVATION_NUMBER])
            ->andFilterWhere(['like', 'CARD_NAME', $this->CARD_NAME])
            ->andFilterWhere(['like', 'COMMENT', $this->COMMENT])
            ->andFilterWhere(['between', 'TIME_STAMP', $params['dateFrom'], $params['dateTo']]);*/
        // grid filtering conditions
        /*$query->andFilterWhere([
            'id' => $this->id,
        ]);*/

        $query->andFilterWhere(['or',
            ['like', 'customer.email', $this->input_search],
            ['like', 'customer.firstname', $this->input_search],
            ['like', 'customer.lastname', $this->input_search],
            ['like', 'customer.address', $this->input_search],
            ['like', 'concat(customer.lastname, \' \', customer.firstname)', $this->input_search],
            ['like', 'concat(customer.firstname, \' \', customer.lastname)', $this->input_search],
            ['between', 'card.updated_at', $this->date_from, date('Y-m-d', strtotime($this->date_to . ' + 1 days'))],
            ['like', 'status.name', $this->input_search],
            ['like', 'product.name', $this->input_search],
        ]);

        /*$query->andFilterWhere(['like', 'customer.email', $this->input_search])
            ->orFilterWhere(['like', 'customer.firstname', $this->input_search])
            ->orFilterWhere(['like', 'customer.lastname', $this->input_search])
            ->orFilterWhere(['between', 'card.updated_at', $this->date_from, date('Y-m-d', strtotime($this->date_to . ' + 1 days'))])
            ->orFilterWhere(['like', 'status.name', $this->input_search]);*/

        if (Yii::$app->user->can('pull_orders')) {
            $query->orFilterWhere(['or',
                ['like', 'token', $this->input_search],
                ['like', 'recid', $this->input_search],
            ]);
        }

        return $dataProvider;
    }
}
